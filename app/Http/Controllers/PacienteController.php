<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\PacienteAdulto;
use App\Models\PacienteAdolescente;
use App\Models\PacienteNino;
use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $pacientes = Paciente::with(['detalle', 'estado', 'user'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('numero_expediente', 'like', '%' . $request->search . '%')
                    ->orWhereHas('detalle', function ($q) use ($request) {
                        $q->where('nombre', 'like', '%' . $request->search . '%')
                            ->orWhere('apellido', 'like', '%' . $request->search . '%');
                    });
            })
            ->when($request->filled('prioridad'), function ($query) use ($request) {
                return $query->where('prioridad', $request->prioridad);
            })
            ->when($request->filled('tipo'), function ($query) use ($request) {
                return $query->where('tipo_paciente', $request->tipo);
            })
            ->when(count($this->tiposAtencionPermitidos()) < 2, function ($query) {
                return $query->whereIn('tipo_atencion', $this->tiposAtencionPermitidos());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pacientes.index', compact('pacientes'));
    }

    public function create()
    {
        return redirect()->route('pacientes.index');
    }

    public function store(Request $request)
    {
        $tipo = $request->tipo_paciente;

        // Convertir fecha de DD/MM/AAAA a YYYY-MM-DD antes de validar
        if ($request->has('fecha_nacimiento') && str_contains($request->fecha_nacimiento, '/')) {
            try {
                $fecha = Carbon::createFromFormat('d/m/Y', $request->fecha_nacimiento);
                $request->merge(['fecha_nacimiento' => $fecha->format('Y-m-d')]);
            } catch (\Exception $e) {
                return back()->with('error', 'Fecha de nacimiento inválida.')->withInput();
            }
        }

        // PRIMERO: Validar campos comunes para TODOS
        $comunData = $request->validate([
            'estado_id' => 'nullable|exists:estados,id',
            'motivo_consulta' => 'nullable|string|max:500',
            'diagnostico_preliminar' => 'nullable|string',
            'prioridad' => 'required|in:baja,media,alta,urgencia',
            'tipo_atencion' => 'required|in:publico,privado',
        ]);

        // Validar permiso de visibilidad sobre el tipo de atención elegido
        if (!$this->puedeTipoAtencion($comunData['tipo_atencion'])) {
            return back()->with('error', 'No tienes permiso para registrar pacientes de tipo "' . $comunData['tipo_atencion'] . '".')->withInput();
        }

        // SEGUNDO: Validar según tipo (campos específicos de cada tabla)
        if ($tipo === 'adulto') {
            $detalleData = $request->validate($this->reglasAdulto());
            // Combinar nacionalidad + cédula
            if ($request->has('nacionalidad')) {
                $detalleData['cedula'] = $request->nacionalidad . $request->cedula;
            }
        } elseif ($tipo === 'adolescente') {
            $detalleData = $request->validate($this->reglasAdolescente());
        } elseif ($tipo === 'niño') {
            $detalleData = $request->validate($this->reglasNino());
        } else {
            return back()->with('error', 'Tipo de paciente no válido.');
        }

        DB::beginTransaction();

        try {
            // 1. Crear detalle según tipo
            $detalle = match ($tipo) {
                'adulto' => PacienteAdulto::create($detalleData),
                'adolescente' => PacienteAdolescente::create($detalleData),
                'niño' => PacienteNino::create($detalleData),
            };

            // 2. Generar expediente
            $numeroExpediente = $this->generarExpediente();

            // 3. Crear paciente base
            Paciente::create([
                'tipo_paciente' => $tipo,
                'paciente_detalle_id' => $detalle->id,
                'paciente_detalle_type' => get_class($detalle),
                'user_id' => null,
                'estado_id' => $comunData['estado_id'] ?? null,
                'numero_expediente' => $numeroExpediente,
                'motivo_consulta' => $comunData['motivo_consulta'] ?? null,
                'diagnostico_preliminar' => $comunData['diagnostico_preliminar'] ?? null,
                'prioridad' => $comunData['prioridad'],
                'tipo_atencion' => $comunData['tipo_atencion'],
            ]);

            DB::commit();

            return redirect()->route('pacientes.index')
                ->with('success', "Paciente {$tipo} registrado exitosamente. Expediente: {$numeroExpediente}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar paciente: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Error al registrar: ' . $e->getMessage())->withInput();
        }
    }

   public function show(Paciente $paciente)
{
    // Bloquear si el rol no tiene permiso para ver este tipo de atención
    abort_unless($this->puedeTipoAtencion($paciente->tipo_atencion), 403, 'No tienes permiso para ver pacientes de tipo "' . $paciente->tipo_atencion . '".');

    // Cargar relaciones básicas
    $paciente->load(['estado', 'citas', 'user', 'notas', 'sesiones.user']);
    
    // Forzar la carga del detalle polimórfico
    try {
        $detalle = $paciente->detalle; // Esto debería cargar la relación
        if (!$detalle && $paciente->paciente_detalle_type && $paciente->paciente_detalle_id) {
            // Si falla, cargar manualmente
            $modelClass = $paciente->paciente_detalle_type;
            $detalle = $modelClass::find($paciente->paciente_detalle_id);
            $paciente->setRelation('detalle', $detalle);
        }
    } catch (\Exception $e) {
        Log::error('Error al cargar detalle del paciente ID ' . $paciente->id . ': ' . $e->getMessage());
        $paciente->setRelation('detalle', null);
    }
    
    return view('pacientes.show', compact('paciente'));
}

public function edit(Paciente $paciente)
{
    abort_unless($this->puedeTipoAtencion($paciente->tipo_atencion), 403, 'No tienes permiso para editar pacientes de tipo "' . $paciente->tipo_atencion . '".');

    $estados = Estado::orderBy('tipo')->get();

        // Buscar detalle manualmente por si la relación polimórfica falla
        $detalle = null;
        if ($paciente->paciente_detalle_type && $paciente->paciente_detalle_id) {
            try {
                $detalle = app($paciente->paciente_detalle_type)->find($paciente->paciente_detalle_id);
            } catch (\Exception $e) {
                // ignorar
            }
        }

        // Asignar el detalle al paciente para que la vista lo use
        $paciente->setRelation('detalle', $detalle);

        return view('pacientes.edit', compact('paciente', 'estados'));
    }
    public function update(Request $request, Paciente $paciente)
    {
        $tipo = $paciente->tipo_paciente;

        // Convertir fecha
        if ($request->has('fecha_nacimiento') && str_contains($request->fecha_nacimiento, '/')) {
            try {
                $fecha = Carbon::createFromFormat('d/m/Y', $request->fecha_nacimiento);
                $request->merge(['fecha_nacimiento' => $fecha->format('Y-m-d')]);
            } catch (\Exception $e) {
                return back()->with('error', 'Fecha inválida.')->withInput();
            }
        }

        // Validar campos comunes PRIMERO
        $comunData = $request->validate([
            'estado_id' => 'nullable|exists:estados,id',
            'motivo_consulta' => 'nullable|string|max:500',
            'diagnostico_preliminar' => 'nullable|string',
            'prioridad' => 'required|in:baja,media,alta,urgencia',
            'tipo_atencion' => 'required|in:publico,privado',
        ]);

        // Bloquear edición si el rol no tiene permiso sobre el tipo actual o el nuevo
        if (!$this->puedeTipoAtencion($paciente->tipo_atencion) || !$this->puedeTipoAtencion($comunData['tipo_atencion'])) {
            return back()->with('error', 'No tienes permiso para editar pacientes de este tipo de atención.')->withInput();
        }

        // Validar según tipo DESPUÉS
        if ($tipo === 'adulto') {
            $detalleData = $request->validate($this->reglasAdultoUpdate($paciente->detalle->id));
            if ($request->has('nacionalidad')) {
                $detalleData['cedula'] = $request->nacionalidad . $request->cedula;
            }
        } elseif ($tipo === 'adolescente') {
            $detalleData = $request->validate($this->reglasAdolescente());
        } elseif ($tipo === 'niño') {
            $detalleData = $request->validate($this->reglasNino());
        }

        DB::beginTransaction();

        try {
            $paciente->detalle->update($detalleData);

            $paciente->update([
                'estado_id' => $comunData['estado_id'] ?? null,
                'motivo_consulta' => $comunData['motivo_consulta'] ?? null,
                'diagnostico_preliminar' => $comunData['diagnostico_preliminar'] ?? null,
                'prioridad' => $comunData['prioridad'],
                'tipo_atencion' => $comunData['tipo_atencion'],
            ]);

            if ($paciente->user) {
                $paciente->user->update([
                    'name' => $paciente->detalle->nombre . ' ' . $paciente->detalle->apellido,
                    'email' => $paciente->email,
                ]);
            }

            DB::commit();
            return redirect()->route('pacientes.index')->with('success', 'Paciente actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar paciente: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Paciente $paciente)
    {
        DB::beginTransaction();
        try {
            // Eliminar usuario asociado si existe
            if ($paciente->user) {
                $paciente->user->delete();
            }

            // Eliminar detalle específico (adulto, adolescente o niño)
            if ($paciente->detalle) {
                $paciente->detalle->delete();
            }

            // Eliminar el paciente base
            $paciente->delete();

            DB::commit();

            return redirect()->route('pacientes.index')
                ->with('success', 'Paciente eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar paciente: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function sinUsuario()
    {
        $pacientes = Paciente::with('detalle')
            ->whereNull('user_id')
            ->get()
            ->map(function ($paciente) {
                return [
                    'id' => $paciente->id,
                    'numero_expediente' => $paciente->numero_expediente,
                    'nombre_completo' => $paciente->detalle ? $paciente->detalle->nombre . ' ' . $paciente->detalle->apellido : 'Sin nombre',
                    'cedula' => $paciente->cedula_paciente,
                    'tipo' => $paciente->tipo_paciente,
                    'email' => $paciente->email,
                ];
            });

        return response()->json(['success' => true, 'pacientes' => $pacientes]);
    }

    // ==========================================
    // MÉTODOS PRIVADOS
    // ==========================================

    /**
     * Tipos de atención (público/privado) que el usuario autenticado puede ver.
     * Se controlan con los permisos pacientes.ver_publico y pacientes.ver_privado.
     *
     * @return array<string>
     */
    private function tiposAtencionPermitidos(): array
    {
        $user = Auth::user();
        $tipos = [];

        if ($user->hasPermission('pacientes.ver_publico')) {
            $tipos[] = 'publico';
        }

        if ($user->hasPermission('pacientes.ver_privado')) {
            $tipos[] = 'privado';
        }

        return $tipos;
    }

    /**
     * Indica si el usuario autenticado puede ver/editar un tipo de atención.
     *
     * @param string $tipo Tipo de atención: publico o privado
     * @return bool
     */
    private function puedeTipoAtencion(string $tipo): bool
    {
        return in_array($tipo, $this->tiposAtencionPermitidos(), true);
    }

    private function generarExpediente(): string
    {
        $ultimo = Paciente::withTrashed()->latest('id')->first();
        $numero = $ultimo ? intval(substr($ultimo->numero_expediente ?? 'EXP-000000', 4)) + 1 : 1;
        return 'EXP-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    // ==========================================
    // REGLAS DE VALIDACIÓN - ADULTO
    // ==========================================

    private function reglasAdulto(): array
    {
        return [
            'nombre' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'apellido' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'cedula' => 'required|string|max:8|regex:/^[0-9]+$/',
            'fecha_nacimiento' => ['required', 'date', function ($attribute, $value, $fail) {
                $fecha = Carbon::parse($value);
                if ($fecha->age < 18) {
                    $fail('El paciente adulto debe tener al menos 18 años.');
                }
            }],
            'genero' => 'nullable|in:Masculino,Femenino,Otro',
            'estado_civil' => 'nullable|in:Soltero/a,Casado/a,Divorciado/a,Viudo/a,Unión Libre',
            'telefono' => 'required|string|max:15|regex:/^\+?[0-9]+$/',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
            'nivel_instruccion' => 'nullable|string|max:50',
            'fecha_entrevista' => 'nullable|date',
            'ocupacion' => 'nullable|string|max:100',
            'lugar_trabajo' => 'nullable|string|max:150',
            'telefono_trabajo' => 'nullable|string|max:15',
            'nombre_emergencia' => 'nullable|string|max:150',
            'telefono_emergencia' => 'nullable|string|max:15',
            'parentesco_emergencia' => 'nullable|string|max:50',
            'historia_problema' => 'nullable|string',
            'frecuencia_sintomas' => 'nullable|string',
            'intensidad_sintomas' => 'nullable|in:Leve,Moderado,Severo',
            'factores_desencadenantes' => 'nullable|string',
            'estrategias_afrontamiento' => 'nullable|string',
            'sensaciones_corporales' => 'nullable|string',
            'autolesiones' => 'nullable|string',
            'ideacion_suicida' => 'nullable|string',
            'consumo_sustancias' => 'nullable|string',
            'estrategias_previas' => 'nullable|string',
            'objetivos_terapia' => 'nullable|string',
            'convivencia' => 'nullable|string',
            'relacion_familiar' => 'nullable|string',
            'conflictos_familiares' => 'nullable|string',
            'vida_social' => 'nullable|string',
            'pareja' => 'nullable|string',
            'redes_sociales' => 'nullable|string',
            'estado_sexual' => 'nullable|string',
            'problemas_sexuales' => 'nullable|string',
            'actividad_sexual' => 'nullable|string',
            'historial_problemas_sexuales' => 'nullable|string',
            'estado_animo' => 'nullable|string',
            'sintomas_depresivos' => 'nullable|string',
            'sintomas_ansiedad' => 'nullable|string',
            'irritabilidad' => 'nullable|string',
            'conductas_riesgo' => 'nullable|string',
            'sintomas_fisicos' => 'nullable|string',
            'calidad_vida' => 'nullable|string',
            'antecedentes_psiquiatricos' => 'nullable|string',
            'medicamentos_actuales' => 'nullable|string',
            'antecedentes_familiares' => 'nullable|string',
            'consume_alcohol' => 'nullable|in:No,Ocasional,Frecuente',
            'consume_tabaco' => 'nullable|in:No,Ocasional,Frecuente',
            'otras_sustancias' => 'nullable|string|max:100',
            'habitos_sueno' => 'nullable|string',
            'fortalezas' => 'nullable|string',
            'logros' => 'nullable|string',
            'red_apoyo' => 'nullable|string',
            'motivacion_cambio' => 'nullable|integer|min:0|max:10',
            'valores_accion' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ];
    }

    private function reglasAdultoUpdate($id): array
    {
        return [
            'nombre' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'apellido' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'cedula' => 'required|string|max:8|regex:/^[0-9]+$/',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'nullable|in:Masculino,Femenino,Otro',
            'estado_civil' => 'nullable|in:Soltero/a,Casado/a,Divorciado/a,Viudo/a,Unión Libre',
            'telefono' => 'required|string|max:15|regex:/^\+?[0-9]+$/',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
            'nivel_instruccion' => 'nullable|string|max:50',
            'fecha_entrevista' => 'nullable|date',
            'ocupacion' => 'nullable|string|max:100',
            'lugar_trabajo' => 'nullable|string|max:150',
            'telefono_trabajo' => 'nullable|string|max:15',
            'nombre_emergencia' => 'nullable|string|max:150',
            'telefono_emergencia' => 'nullable|string|max:15',
            'parentesco_emergencia' => 'nullable|string|max:50',
            'historia_problema' => 'nullable|string',
            'frecuencia_sintomas' => 'nullable|string',
            'intensidad_sintomas' => 'nullable|in:Leve,Moderado,Severo',
            'factores_desencadenantes' => 'nullable|string',
            'estrategias_afrontamiento' => 'nullable|string',
            'sensaciones_corporales' => 'nullable|string',
            'autolesiones' => 'nullable|string',
            'ideacion_suicida' => 'nullable|string',
            'consumo_sustancias' => 'nullable|string',
            'estrategias_previas' => 'nullable|string',
            'objetivos_terapia' => 'nullable|string',
            'convivencia' => 'nullable|string',
            'relacion_familiar' => 'nullable|string',
            'conflictos_familiares' => 'nullable|string',
            'vida_social' => 'nullable|string',
            'pareja' => 'nullable|string',
            'redes_sociales' => 'nullable|string',
            'estado_sexual' => 'nullable|string',
            'problemas_sexuales' => 'nullable|string',
            'actividad_sexual' => 'nullable|string',
            'historial_problemas_sexuales' => 'nullable|string',
            'estado_animo' => 'nullable|string',
            'sintomas_depresivos' => 'nullable|string',
            'sintomas_ansiedad' => 'nullable|string',
            'irritabilidad' => 'nullable|string',
            'conductas_riesgo' => 'nullable|string',
            'sintomas_fisicos' => 'nullable|string',
            'calidad_vida' => 'nullable|string',
            'antecedentes_psiquiatricos' => 'nullable|string',
            'medicamentos_actuales' => 'nullable|string',
            'antecedentes_familiares' => 'nullable|string',
            'consume_alcohol' => 'nullable|in:No,Ocasional,Frecuente',
            'consume_tabaco' => 'nullable|in:No,Ocasional,Frecuente',
            'otras_sustancias' => 'nullable|string|max:100',
            'habitos_sueno' => 'nullable|string',
            'fortalezas' => 'nullable|string',
            'logros' => 'nullable|string',
            'red_apoyo' => 'nullable|string',
            'motivacion_cambio' => 'nullable|integer|min:0|max:10',
            'valores_accion' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ];
    }

    // ==========================================
    // REGLAS DE VALIDACIÓN - ADOLESCENTE
    // ==========================================

    private function reglasAdolescente(): array
    {
        return [
            'nombre' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'apellido' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'cedula' => 'nullable|string|max:20',
            'fecha_nacimiento' => ['required', 'date', function ($attribute, $value, $fail) {
                $fecha = Carbon::parse($value);
                $edad = $fecha->age;
                if ($edad < 13 || $edad > 17) {
                    $fail('El paciente adolescente debe tener entre 13 y 17 años.');
                }
            }],
            'genero' => 'nullable|in:Masculino,Femenino,Otro',
            'telefono_personal' => 'nullable|string|max:15',
            'email_personal' => 'nullable|email|max:100',
            'institucion_educativa' => 'nullable|string|max:100',
            'nivel_educativo' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:255',
            'grado_instruccion' => 'nullable|string|max:50',
            'fecha_entrevista' => 'nullable|date',

            // Representante
            'nombre_representante' => 'nullable|string|max:150',
            'cedula_representante' => 'nullable|string|max:20',
            'telefono_representante' => 'nullable|string|max:15',
            'email_representante' => 'nullable|email|max:100',
            'parentesco' => 'nullable|string|max:50',

            // Familia
            'padre_nombre' => 'nullable|string|max:150',
            'padre_edad' => 'nullable|integer|min:20|max:99',
            'padre_ocupacion' => 'nullable|string|max:100',
            'madre_nombre' => 'nullable|string|max:150',
            'madre_edad' => 'nullable|integer|min:20|max:99',
            'madre_ocupacion' => 'nullable|string|max:100',
            'hermanos' => 'nullable|string',
            'personas_vive' => 'nullable|string',

            // Motivo de consulta
            'historia_problema' => 'nullable|string',
            'frecuencia_sintomas' => 'nullable|string',
            'intensidad_sintomas' => 'nullable|in:Leve,Moderado,Severo',
            'factores_desencadenantes' => 'nullable|string',
            'pads_observaciones' => 'nullable|string',

            // Historia del problema
            'cuando_empezo' => 'nullable|string',
            'cambios_rendimiento' => 'nullable|string',

            // Hábitos y relaciones
            'habitos' => 'nullable|string',
            'relaciones_sociales' => 'nullable|string',
            'rendimiento_academico' => 'nullable|string|max:100',
            'tiene_amigos' => 'nullable|string|max:50',
            'actividades_grupales' => 'nullable|string|max:50',
            'rechazo_bullying' => 'nullable|string',
            'pareja' => 'nullable|string',
            'redes_sociales' => 'nullable|string',
            'mensajes_dano' => 'nullable|string',

            // Salud
            'salud_fisica' => 'nullable|string',
            'terapia_anterior' => 'nullable|string',

            // Consumo
            'sustancias_frecuencia' => 'nullable|string',
            'reducir_consumo' => 'nullable|string',
            'molesta_criticas' => 'nullable|string',
            'usar_para_olvidar' => 'nullable|string',
            'olvidar_lo_que_paso' => 'nullable|string',

            // Sexualidad
            'orientacion_sexual' => 'nullable|string',

            // Evaluación emocional
            'depresion' => 'nullable|string',
            'ansiedad_panico' => 'nullable|string',
            'irritabilidad_impulso' => 'nullable|string',
            'conductas_riesgo' => 'nullable|string',
            'autoimagen' => 'nullable|string',
            'relacion_padres' => 'nullable|string',
            'manejo_emociones' => 'nullable|string',
            'antecedentes_familiares' => 'nullable|string',

            // Recursos
            'que_haces_bien' => 'nullable|string',
            'que_te_da_orgullo' => 'nullable|string',
            'quien_te_respalda' => 'nullable|string',
            'cuanto_quieres_cambio' => 'nullable|integer|min:0|max:10',
            'mini_ejercicio' => 'nullable|string',

            // Observaciones
            'observaciones' => 'nullable|string',
        ];
    }

    // ==========================================
    // REGLAS DE VALIDACIÓN - NIÑO
    // ==========================================

    private function reglasNino(): array
    {
        return [
            'nombre' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'apellido' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'fecha_nacimiento' => ['required', 'date', function ($attribute, $value, $fail) {
                $fecha = Carbon::parse($value);
                if ($fecha->age > 11) {
                    $fail('El paciente niño debe tener máximo 11 años.');
                }
            }],
            'telefono_contacto' => 'nullable|string|max:15',
            'grado_instruccion' => 'nullable|string|max:50',
            'maestro' => 'nullable|string|max:100',
            'direccion' => 'nullable|string|max:255',
            'nombre_padre' => 'nullable|string|max:150',
            'edad_padre' => 'nullable|integer|min:18|max:99',
            'ocupacion_padre' => 'nullable|string|max:100',
            'nombre_madre' => 'nullable|string|max:150',
            'edad_madre' => 'nullable|integer|min:18|max:99',
            'ocupacion_madre' => 'nullable|string|max:100',
            'hermanos' => 'nullable|string|max:255',
            'personas_vive' => 'nullable|string|max:255',
            'desarrollo_problema' => 'nullable|string',
            'frecuencia_sintomas' => 'nullable|in:Diario,Semanal,Mensual,Ocasional',
            'intensidad_sintomas' => 'nullable|in:Leve,Moderado,Severo',
            'duracion_sintomas' => 'nullable|string|max:100',
            'contexto_problema' => 'nullable|string|max:255',
            'estrategias_afrontamiento' => 'nullable|string',
            'consecuencias' => 'nullable|string|max:255',
            'acontecimientos_estresantes' => 'nullable|string',
            'expectativas' => 'nullable|string',
            'tipo_parto' => 'nullable|in:Natural,Cesárea,Inducido',
            'causa_parto_inducido' => 'nullable|string|max:255',
            'dificultades_parto' => 'nullable|string',
            'comportamiento_bebe' => 'nullable|string|max:255',
            'reacciones_estimulos' => 'nullable|string|max:255',
            'situacion_familiar_postnatal' => 'nullable|string|max:255',
            'aceptacion_maternidad' => 'nullable|string',
            'desarrollo_areas' => 'nullable|string',
            'habla_lenguaje' => 'nullable|string',
            'alimentacion_bebe' => 'nullable|string|max:255',
            'dificultades_destete' => 'nullable|string|max:255',
            'trastornos_alimentacion' => 'nullable|string|max:255',
            'alimentacion_actual' => 'nullable|string',
            'descripcion_dia_anterior' => 'nullable|string',
            'info_sexualidad' => 'nullable|string',
            'desarrollo_sexual' => 'nullable|string|max:255',
            'tareas_escolares' => 'nullable|string',
            'rendimiento_escolar' => 'nullable|string|max:100',
            'responsabilidades_domesticas' => 'nullable|string|max:255',
            'recompensas' => 'nullable|string|max:255',
            'nivel_actividad' => 'nullable|in:Alto,Medio,Bajo',
            'entretenimiento' => 'nullable|string|max:255',
            'tipo_actividades' => 'nullable|in:Sedentarias,Inquietas,Mixtas',
            'finalizacion_tareas' => 'nullable|in:Sí\\, siempre,A veces,No',
            'arrebatos' => 'nullable|string',
            'oposicionismo' => 'nullable|string',
            'agresiones' => 'nullable|string',
            'autoimagen' => 'nullable|string',
            'percepcion_demas' => 'nullable|string',
            'relaciones_ninos' => 'nullable|string',
            'tiene_amigos' => 'nullable|in:Sí,No,Pocos',
            'actividades_sociales' => 'nullable|string|max:255',
            'tipo_ninos_atraen' => 'nullable|string|max:255',
            'comportamiento_grupo' => 'nullable|string',
            'juegos_preferidos' => 'nullable|string|max:255',
            'relacion_adultos' => 'nullable|string',
            'entretenimiento_casa' => 'nullable|string|max:255',
            'actividades_exteriores' => 'nullable|string|max:255',
            'actividades_deportivas' => 'nullable|string|max:255',
            'intereses' => 'nullable|string',
        ];
    }
}
