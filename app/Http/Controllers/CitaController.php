<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CitaController extends Controller
{
    /**
     * Muestra el listado de citas organizadas en tres categorías:
     * citas de hoy, próximas citas y citas canceladas recientemente.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Citas programadas para el día de hoy (estado pendiente)
        $citasHoy = Cita::with('paciente')
            ->whereDate('fecha', today())
            ->where('estado', 'pendiente')
            ->orderBy('fecha', 'asc')
            ->get();
        
        // Citas futuras (después de hoy) en estado pendiente
        $citas = Cita::with('paciente')
            ->whereDate('fecha', '>', today())
            ->where('estado', 'pendiente')
            ->orderBy('fecha', 'asc')
            ->get();
        
        // Citas canceladas en los últimos 30 días
        $citasCanceladas = Cita::with('paciente')
            ->where('estado', 'cancelada')
            ->whereDate('updated_at', '>=', now()->subDays(30))
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // Retornar vista con las tres colecciones de citas
        return view('citas.index', compact('citasHoy', 'citas', 'citasCanceladas'));
    }
    
    /**
     * Muestra el formulario para crear una nueva cita.
     * Si se recibe el parámetro 'reprogramar', carga los datos de la cita original.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        // Obtener todos los pacientes ordenados alfabéticamente
        $pacientes = Paciente::orderBy('nombre_completo')->get();
        
        $citaOriginal = null;
        $reprogramando = false;
        
        // Verificar si se está reprogramando una cita cancelada
        if ($request->has('reprogramar')) {
            $citaOriginal = Cita::with('paciente')->find($request->reprogramar);
            if ($citaOriginal) {
                $reprogramando = true; // Bandera para indicar modo reprogramación
            }
        }
        
        return view('citas.create', compact('pacientes', 'citaOriginal', 'reprogramando'));
    }
    
    /**
     * Cuenta cuántas citas activas hay en una fecha específica.
     * Excluye las citas canceladas del conteo.
     * 
     * @param string $fecha Fecha en formato Y-m-d
     * @return int Número de citas en esa fecha
     */
    private function contarCitasPorFecha($fecha)
    {
        return Cita::whereDate('fecha', $fecha)
            ->where('estado', '!=', 'cancelada') // No contar las canceladas
            ->count();
    }
    
    /**
     * Consulta los cupos disponibles para una fecha específica.
     * Responde en formato JSON para peticiones AJAX.
     * El límite máximo es de 4 citas por día.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cuposDisponibles(Request $request)
    {
        $fecha = $request->get('fecha');
        
        // Validar que se proporcionó la fecha
        if (!$fecha) {
            return response()->json([
                'success' => false,
                'message' => 'Fecha no proporcionada'
            ]);
        }
        
        // Calcular cupos disponibles
        $citasEnFecha = $this->contarCitasPorFecha($fecha);
        $cuposDisponibles = 4 - $citasEnFecha; // Máximo 4 cupos por día
        
        // Retornar información detallada de cupos
        return response()->json([
            'success' => true,
            'fecha' => $fecha,
            'citas_agendadas' => $citasEnFecha,
            'cupos_disponibles' => $cuposDisponibles,
            'max_cupos' => 4,
            'disponible' => $cuposDisponibles > 0 // true si hay al menos un cupo
        ]);
    }
    
    /**
     * Verifica si un paciente ya tiene una cita en una fecha específica.
     * También retorna información de cupos disponibles.
     * Responde en formato JSON para peticiones AJAX.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verificarCitaPaciente(Request $request)
    {
        $fecha = $request->get('fecha');
        $pacienteId = $request->get('paciente_id');
        $citaOriginalId = $request->get('cita_original_id');
        
        // Validar parámetros requeridos
        if (!$fecha || !$pacienteId) {
            return response()->json([
                'success' => false,
                'message' => 'Faltan parámetros: fecha y paciente_id son requeridos'
            ]);
        }
        
        // Verificar si es una reprogramación y la fecha es la misma que la cita original
        $mismaFechaOriginal = false;
        if ($citaOriginalId) {
            $citaOriginal = Cita::find($citaOriginalId);
            if ($citaOriginal && $citaOriginal->fecha->format('Y-m-d') === $fecha) {
                $mismaFechaOriginal = true; // Es la misma fecha de la cita que se canceló
            }
        }
        
        // Verificar si el paciente ya tiene otra cita en esa fecha
        $tieneCita = Cita::where('paciente_id', $pacienteId)
            ->whereDate('fecha', $fecha)
            ->where('estado', '!=', 'cancelada')
            ->exists();
        
        // Calcular cupos disponibles en la fecha
        $citasEnFecha = $this->contarCitasPorFecha($fecha);
        $cuposDisponibles = 4 - $citasEnFecha;
        
        return response()->json([
            'success' => true,
            'tiene_cita' => $tieneCita,
            'misma_fecha_original' => $mismaFechaOriginal,
            'citas_agendadas' => $citasEnFecha,
            'cupos_disponibles' => $cuposDisponibles,
            'max_cupos' => 4
        ]);
    }
    
    /**
     * Almacena una nueva cita en la base de datos.
     * Incluye validaciones para:
     * - No permitir reprogramación en la misma fecha
     * - Límite de 4 cupos diarios
     * - No permitir dos citas del mismo paciente el mismo día
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validar datos básicos del formulario
        $validated = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'fecha' => 'required|date|after_or_equal:today', // No permitir fechas pasadas
            'objetivo' => 'nullable|string|max:500',
            'planificacion' => 'nullable|string|max:1000',
            'cita_original_id' => 'nullable|exists:citas,id' // ID de la cita que se está reprogramando
        ]);
        
        // ====================================================
        // VALIDACIÓN 1: REPROGRAMACIÓN - NO PUEDE SER EL MISMO DÍA
        // ====================================================
        
        if ($request->filled('cita_original_id')) {
            $citaOriginal = Cita::findOrFail($request->cita_original_id);
            
            // Verificar que no se reprograme para el mismo día de la cita original
            if ($citaOriginal->fecha->format('Y-m-d') === $validated['fecha']) {
                // Respuesta para peticiones AJAX
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No puedes reprogramar la cita para el mismo día de la cita original cancelada.'
                    ], 422); // Código HTTP 422: Entidad no procesable
                }
                
                // Respuesta para peticiones normales
                return redirect()->back()
                    ->with('error', 'No puedes reprogramar la cita para el mismo día de la cita original cancelada.')
                    ->withInput(); // Mantener datos ingresados
            }
        }
        
        // ====================================================
        // VALIDACIÓN 2: CUPOS DIARIOS (máximo 4 citas por día)
        // ====================================================
        
        $citasEnFecha = $this->contarCitasPorFecha($validated['fecha']);
        
        if ($citasEnFecha >= 4) {
            // Respuesta para peticiones AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay cupos disponibles para esta fecha. Ya hay 4 citas agendadas.'
                ], 422);
            }
            
            // Respuesta para peticiones normales
            return redirect()->back()
                ->with('error', 'No hay cupos disponibles para esta fecha. Ya hay 4 citas agendadas.')
                ->withInput();
        }
        
        // ====================================================
        // VALIDACIÓN 3: MISMO PACIENTE NO PUEDE TENER DOS CITAS EL MISMO DÍA
        // ====================================================
        
        $citaExistente = Cita::where('paciente_id', $validated['paciente_id'])
            ->whereDate('fecha', $validated['fecha'])
            ->where('estado', '!=', 'cancelada') // Ignorar citas canceladas
            ->first();
        
        if ($citaExistente) {
            // Respuesta para peticiones AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este paciente ya tiene una cita programada para esta fecha. No puede tener dos citas el mismo día.'
                ], 422);
            }
            
            // Respuesta para peticiones normales
            return redirect()->back()
                ->with('error', 'Este paciente ya tiene una cita programada para esta fecha. No puede tener dos citas el mismo día.')
                ->withInput();
        }
        
        // ====================================================
        // CREAR LA CITA (todas las validaciones pasaron)
        // ====================================================
        
        $cita = Cita::create([
            'paciente_id' => $validated['paciente_id'],
            'fecha' => $validated['fecha'],
            'objetivo' => $validated['objetivo'] ?? null,
            'planificacion' => $validated['planificacion'] ?? null,
            'estado' => 'pendiente' // Estado inicial: pendiente
        ]);
        
        // Registrar en el log si es una reprogramación
        if ($request->filled('cita_original_id')) {
            Log::info('Cita reprogramada', [
                'original_id' => $request->cita_original_id,
                'nueva_id' => $cita->id,
                'paciente_id' => $validated['paciente_id'],
                'fecha_original' => $citaOriginal->fecha->format('Y-m-d'),
                'fecha_nueva' => $validated['fecha']
            ]);
        }
        
        // Personalizar mensaje según sea creación o reprogramación
        $mensaje = $request->has('cita_original_id') 
            ? 'Cita reprogramada correctamente para el ' . date('d/m/Y', strtotime($validated['fecha']))
            : 'Cita creada correctamente';
        
        // Redirigir al listado de citas con mensaje de éxito
        return redirect()->route('citas.index')
            ->with('success', $mensaje);
    }
    
    /**
     * Muestra los detalles de una cita específica.
     * 
     * @param \App\Models\Cita $cita
     * @return \Illuminate\View\View
     */
    public function show(Cita $cita)
    {
        return view('citas.show', compact('cita'));
    }
    
    /**
     * Muestra el formulario para editar una cita existente.
     * 
     * @param \App\Models\Cita $cita
     * @return \Illuminate\View\View
     */
    public function edit(Cita $cita)
    {
        // Obtener lista de pacientes para el selector
        $pacientes = Paciente::orderBy('nombre_completo')->get();
        
        return view('citas.edit', compact('cita', 'pacientes'));
    }
    
    /**
     * Actualiza una cita existente.
     * Maneja diferentes escenarios:
     * - Cambio de fecha (con validaciones de cupos)
     * - Cancelación de cita (requiere motivo)
     * - Cambio de estado (atendida/no asistió)
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Cita $cita
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Cita $cita)
    {
        // Si se está cambiando la fecha, validar cupos y duplicados
        if ($request->has('fecha') && $request->fecha != $cita->fecha) {
            
            // VALIDACIÓN 1: Verificar cupos disponibles en la nueva fecha
            // Se excluye la cita actual del conteo
            $citasEnFecha = Cita::whereDate('fecha', $request->fecha)
                ->where('estado', '!=', 'cancelada')
                ->where('id', '!=', $cita->id) // Excluir la cita que se está editando
                ->count();
            
            if ($citasEnFecha >= 4) {
                // Respuesta para peticiones AJAX
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No hay cupos disponibles para esta fecha. Ya hay 4 citas agendadas.'
                    ], 422);
                }
                
                // Respuesta para peticiones normales
                return redirect()->back()
                    ->with('error', 'No hay cupos disponibles para esta fecha. Ya hay 4 citas agendadas.')
                    ->withInput();
            }
            
            // VALIDACIÓN 2: Verificar que el paciente no tenga otra cita en la nueva fecha
            $citaExistente = Cita::where('paciente_id', $cita->paciente_id)
                ->whereDate('fecha', $request->fecha)
                ->where('estado', '!=', 'cancelada')
                ->where('id', '!=', $cita->id) // Excluir la cita actual
                ->first();
            
            if ($citaExistente) {
                // Respuesta para peticiones AJAX
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Este paciente ya tiene una cita programada para esta fecha. No puede tener dos citas el mismo día.'
                    ], 422);
                }
                
                // Respuesta para peticiones normales
                return redirect()->back()
                    ->with('error', 'Este paciente ya tiene una cita programada para esta fecha. No puede tener dos citas el mismo día.')
                    ->withInput();
            }
        }
        
        // ====================================================
        // MANEJAR CANCELACIÓN DE CITA
        // ====================================================
        if ($request->estado === 'cancelada') {
            // Validar que se proporcione un motivo de cancelación
            $request->validate([
                'estado' => 'required|in:cancelada',
                'motivo_cancelacion' => 'required|string|min:5|max:500' // Mínimo 5 caracteres
            ]);
            
            // Usar transacción para guardar la cancelación
            DB::transaction(function () use ($request, $cita) {
                $cita->motivo_cancelacion = $request->motivo_cancelacion;
                $cita->estado = 'cancelada';
                $cita->save();
            });
            
            // Verificar si se solicita reprogramar después de cancelar
            $reprogramar = $request->input('reprogramar', false);
            
            if ($reprogramar || $request->has('reprogramar')) {
                // Respuesta para peticiones AJAX: redirigir a crear cita con datos de reprogramación
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'reprogramar' => true,
                        'cita_id' => $cita->id,
                        'message' => 'Cita cancelada. Redirigiendo a reprogramación...'
                    ]);
                }
                
                // Redirigir al formulario de creación con el parámetro de reprogramación
                return redirect()->route('citas.create', ['reprogramar' => $cita->id])
                    ->with('info', 'Cita cancelada. Ahora puedes agendar una nueva fecha.');
            }
            
            // Respuesta para peticiones AJAX: cancelación simple
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cita cancelada correctamente'
                ]);
            }
            
            // Redirigir al listado de citas
            return redirect()->route('citas.index')
                ->with('success', 'Cita cancelada correctamente');
        }
        
        // ====================================================
        // ACTUALIZAR ESTADO DE CITA (atendida/no asistió)
        // ====================================================
        $request->validate([
            'estado' => 'required|in:atendida,no_asistio'
        ]);
        
        // Actualizar el estado de la cita
        $cita->estado = $request->estado;
        $cita->save();
        
        // Respuesta para peticiones AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cita actualizada correctamente'
            ]);
        }
        
        // Redirigir al listado de citas
        return redirect()->route('citas.index')
            ->with('success', 'Cita actualizada correctamente');
    }
    
    /**
     * Muestra solo las citas canceladas en una vista independiente.
     * Utiliza paginación de 15 resultados por página.
     * 
     * @return \Illuminate\View\View
     */
    public function canceladas()
    {
        // Obtener citas canceladas ordenadas por fecha de actualización
        $citasCanceladas = Cita::with('paciente')
            ->where('estado', 'cancelada')
            ->orderBy('updated_at', 'desc')
            ->paginate(15); // Paginar resultados
        
        return view('citas.canceladas', compact('citasCanceladas'));
    }
    
    /**
     * Elimina una cita de la base de datos.
     * 
     * @param \App\Models\Cita $cita
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Cita $cita)
    {
        // Eliminar la cita
        $cita->delete();
        
        // Redirigir al listado con mensaje de éxito
        return redirect()->route('citas.index')
            ->with('success', 'Cita eliminada correctamente');
    }
}