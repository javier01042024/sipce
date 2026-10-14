<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;
use App\Models\Estado;
use App\Models\Paciente;
use App\Models\PacienteNino;
use App\Models\PacienteAdolescente;
use App\Models\PacienteAdulto;
use App\Models\Acompanante;
use App\Models\Cita;
use App\Models\Diario;
use App\Models\Nota;
use App\Models\Diagnostico;
use App\Models\PlanTratamiento;
use App\Models\PlanObjetivo;
use App\Models\Sesion;
use App\Models\Notificacion;
use App\Models\Bitacora;
use App\Models\PatientActivityLog;

/**
 * Siembra de datos de prueba en todo el sistema.
 * Uso: php artisan db:seed --class=TestDataSeeder
 * NO se ejecuta por defecto (no incluido en DatabaseSeeder).
 */
class TestDataSeeder extends Seeder
{
    /** @var array<int, User> Cuenta de portal del paciente, indexada por paciente_id */
    private array $cuentasPortal = [];

    public function run(): void
    {
        $this->command->info('🧪 Sembrando datos de prueba...');

        $this->sembrarEstados();

        $users = $this->sembrarUsuarios();

        $pacientes = $this->sembrarPacientes($users);

        $acompanantes = $this->sembrarAcompanantes($pacientes);

        $diarios = $this->sembrarDiarios($pacientes);

        $citas = $this->sembrarCitas($pacientes);

        $this->sembrarDiagnosticos($pacientes, $users);

        $this->sembrarNotas($pacientes, $users, $diarios);

        $planes = $this->sembrarPlanes($pacientes, $users);

        $this->sembrarObjetivos($planes);

        $this->sembrarSesiones($pacientes, $citas, $acompanantes, $users);

        $this->sembrarNotificaciones($users);

        $this->sembrarActividadPortal($pacientes);

        $this->sembrarBitacora($users, $pacientes);

        $this->command->info('✅ Datos de prueba insertados.');
    }

    private function fechar($model, $cuando): void
    {
        $model->created_at = $cuando;
        $model->updated_at = $cuando;
        $model->save();
    }

    /**
     * Crea (o recupera) la cuenta de portal del paciente y le asigna el rol paciente.
     */
    private function crearCuentaPortal($detalle, ?int $rolPacId): User
    {
        $nombre = trim(($detalle->nombre ?? '') . ' ' . ($detalle->apellido ?? ''));
        $email = Str::slug($detalle->nombre . ' ' . $detalle->apellido, '.') . '@example.com';

        $user = User::firstOrCreate(['email' => $email], [
            'name' => $nombre,
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        if (empty($user->uuid)) {
            $user->uuid = (string) Str::uuid();
        }
        $user->save();

        if ($rolPacId) {
            $user->roles()->syncWithoutDetaching([$rolPacId]);
        }

        return $user;
    }

    private function sembrarEstados(): void
    {
        if (Estado::count() > 0) {
            $this->command->info('  - estados: ya existen, se omiten');
            return;
        }
        $datos = [
            ['tipo' => 'Nuevo', 'descripcion' => 'Ingreso reciente, primera valoración pendiente', 'permite_citas' => true],
            ['tipo' => 'En seguimiento', 'descripcion' => 'Paciente en tratamiento activo', 'permite_citas' => true],
            ['tipo' => 'Estable', 'descripcion' => 'Evolución favorable, sesiones de mantenimiento', 'permite_citas' => true],
            ['tipo' => 'Derivado', 'descripcion' => 'Referido a otro especialista o institución', 'permite_citas' => false],
            ['tipo' => 'En espera', 'descripcion' => 'En lista de espera por agenda', 'permite_citas' => true],
            ['tipo' => 'Inactivo', 'descripcion' => 'Abandonó o cerró el expediente', 'permite_citas' => false],
        ];
        foreach ($datos as $d) {
            Estado::create($d);
        }
        $this->command->info('  - estados: ' . count($datos));
    }

    private function sembrarUsuarios(): array
    {
        $pass = Hash::make('password123');

        $psicologa = User::firstOrCreate(['email' => 'psicologa@example.com'], [
            'name' => 'Dra. Carolina Méndez',
            'password' => $pass,
        ]);
        $secretaria = User::firstOrCreate(['email' => 'secretaria@example.com'], [
            'name' => 'Sra. Elizabeth Rojas',
            'password' => $pass,
        ]);

        if (empty($psicologa->uuid)) { $psicologa->uuid = (string) Str::uuid(); $psicologa->save(); }
        if (empty($secretaria->uuid)) { $secretaria->uuid = (string) Str::uuid(); $secretaria->save(); }

        $rolPsi = Role::where('slug', 'psicologa')->first();
        $rolSec = Role::where('slug', 'secretaria')->first();
        if ($rolPsi) $psicologa->roles()->syncWithoutDetaching([$rolPsi->id]);
        if ($rolSec) $secretaria->roles()->syncWithoutDetaching([$rolSec->id]);

        $psicologa->forceFill([
            'theme_color' => '#7c3aed',
            'last_login_at' => now()->subHours(5),
        ])->save();
        $secretaria->forceFill([
            'theme_color' => '#0ea5e9',
            'dark_mode' => 1,
            'last_login_at' => now()->subDay(),
        ])->save();

        $admin = User::where('email', 'admin@example.com')->first();
        if ($admin) {
            $admin->forceFill(['last_login_at' => now()->subHours(2)])->save();
        }

        // El rol 'paciente' se crea aquí; las cuentas de portal se generan por paciente.
        $rolPac = Role::where('slug', 'paciente')->first();
        if (!$rolPac) {
            $rolPac = Role::create([
                'name' => 'Paciente',
                'slug' => 'paciente',
                'description' => 'Portal del paciente',
                'permissions' => [],
            ]);
        }

        return [
            'psicologa' => $psicologa,
            'secretaria' => $secretaria,
            'admin' => $admin,
            'rol_paciente' => $rolPac,
        ];
    }

    private function sembrarPacientes(array $users): array
    {
        if (Paciente::count() > 0) {
            $this->command->info('  - pacientes: ya existen, se reutilizan');

            $rolPac = $users['rol_paciente'] ?? Role::where('slug', 'paciente')->first();
            foreach (Paciente::with('detalle')->get() as $pac) {
                if (!$pac->user && $pac->detalle) {
                    $cuenta = $this->crearCuentaPortal($pac->detalle, $rolPac?->id);
                    $pac->user_id = $cuenta->id;
                    $pac->save();
                }
                if ($pac->user) {
                    $this->cuentasPortal[$pac->id] = $pac->user;
                }
            }

            return [
                'nino' => Paciente::where('tipo_paciente', 'niño')->get(),
                'adolescente' => Paciente::where('tipo_paciente', 'adolescente')->get(),
                'adulto' => Paciente::where('tipo_paciente', 'adulto')->get(),
            ];
        }

        $ultimo = Paciente::max('numero_expediente');
        $n = $ultimo ? intval(substr($ultimo, 4)) + 1 : 1;

        $psicologa = $users['psicologa'];
        $secretaria = $users['secretaria'];
        $rolPac = $users['rol_paciente'] ?? null;

        $estados = Estado::pluck('id', 'tipo')->toArray();
        $estSeg = $estados['En seguimiento'] ?? null;
        $estNue = $estados['Nuevo'] ?? null;
        $estEst = $estados['Estable'] ?? null;

        $group = ['nino' => [], 'adolescente' => [], 'adulto' => []];

        $crearPaciente = function ($tipo, $detalle, $motivo, $preliminar, $prioridad, $atencion, $municipio, $creadoPor, $estadoId) use (&$n, $rolPac) {
            $pac = new Paciente();
            $pac->forceFill([
                'tipo_paciente' => $tipo,
                'numero_expediente' => 'EXP-' . str_pad($n++, 6, '0', STR_PAD_LEFT),
                'motivo_consulta' => $motivo,
                'diagnostico_preliminar' => $preliminar,
                'prioridad' => $prioridad,
                'tipo_atencion' => $atencion,
                'municipio' => $municipio,
                'estado_id' => $estadoId,
                'paciente_detalle_id' => $detalle->id,
                'paciente_detalle_type' => get_class($detalle),
            ]);
            $pac->save();

            // Cuenta de portal del propio paciente (pacientes.user_id)
            $cuenta = $this->crearCuentaPortal($detalle, $rolPac?->id);
            $pac->user_id = $cuenta->id;
            $pac->save();
            $this->cuentasPortal[$pac->id] = $cuenta;

            return $pac;
        };

        // ===== NIÑOS =====
        $n1 = PacienteNino::create([
            'nombre' => 'Mateo', 'apellido' => 'García', 'fecha_nacimiento' => '2016-03-14',
            'telefono_contacto' => '0412-1112233', 'grado_instruccion' => '2do grado',
            'nombre_padre' => 'Pedro García', 'nombre_madre' => 'Laura García',
            'frecuencia_sintomas' => 'Diario', 'intensidad_sintomas' => 'Severo',
            'tipo_parto' => 'Cesárea', 'tareas_escolares' => 'Requiere apoyo constante',
            'rendimiento_escolar' => 'Bajo', 'tiene_amigos' => 'Pocos',
            'nivel_actividad' => 'Alto', 'intereses' => 'Fútbol, dibujo',
        ]);
        $pN1 = $crearPaciente('niño', $n1, 'Dificultades de atención e impulsividad en el aula',
            'Alto nivel de actividad y baja tolerancia a la frustración', 'media', 'publico', 'Chacao', $secretaria, $estSeg);
        $group['nino'][] = $pN1;

        $n2 = PacienteNino::create([
            'nombre' => 'Valentina', 'apellido' => 'Pérez', 'fecha_nacimiento' => '2015-09-02',
            'telefono_contacto' => '0414-2223344', 'grado_instruccion' => '4to grado',
            'nombre_padre' => 'Luis Pérez', 'nombre_madre' => 'Marta Pérez',
            'frecuencia_sintomas' => 'Semanal', 'intensidad_sintomas' => 'Moderado',
            'tipo_parto' => 'Natural', 'tiene_amigos' => 'Sí',
            'acontecimientos_estresantes' => 'Cambio de colegio a mitad de año',
        ]);
        $pN2 = $crearPaciente('niño', $n2, 'Ansiedad de separación al inicio del año escolar',
            'Somatización recurrente (dolor abdominal) en contexto escolar', 'urgencia', 'privado', 'Baruta', $psicologa, $estSeg);
        $group['nino'][] = $pN2;

        $n3 = PacienteNino::create([
            'nombre' => 'Sebastián', 'apellido' => 'Fuentes', 'fecha_nacimiento' => '2017-01-25',
            'telefono_contacto' => '0424-3334455', 'grado_instruccion' => 'Inicial',
            'frecuencia_sintomas' => 'Ocasional', 'intensidad_sintomas' => 'Leve',
            'nivel_actividad' => 'Medio', 'oposicionismo' => 'Resistencia a normas en casa',
        ]);
        $pN3 = $crearPaciente('niño', $n3, 'Conductas oposicionistas en el hogar',
            'Crisis de enojo breves, sin agresividad física', 'baja', 'publico', 'Libertador', $secretaria, $estNue);
        $group['nino'][] = $pN3;

        $n4 = PacienteNino::create([
            'nombre' => 'Sofía', 'apellido' => 'Bravo', 'fecha_nacimiento' => '2014-06-10',
            'telefono_contacto' => '0416-4445566', 'grado_instruccion' => '5to grado',
            'frecuencia_sintomas' => 'Mensual', 'intensidad_sintomas' => 'Leve',
            'tipo_parto' => 'Inducido', 'causa_parto_inducido' => 'Postérmino',
            'tiene_amigos' => 'Sí', 'autoimagen' => 'Se percibe triste desde la separación',
        ]);
        $pN4 = $crearPaciente('niño', $n4, 'Bajo rendimiento escolar tras separación de los padres',
            'Cambios de humor y retraimiento', 'media', 'privado', 'Sucre', $psicologa, $estNue);
        $group['nino'][] = $pN4;

        // ===== ADOLESCENTES =====
        $a1 = PacienteAdolescente::create([
            'nombre' => 'Daniela', 'apellido' => 'Rojas', 'fecha_nacimiento' => '2008-11-20',
            'cedula' => '30456789', 'genero' => 'Femenino',
            'institucion_educativa' => 'Liceo Simón Bolívar', 'nivel_educativo' => '4to año',
            'nombre_representante' => 'María Rojas', 'telefono_representante' => '0412-5556677',
            'frecuencia_sintomas' => 'Diario', 'intensidad_sintomas' => 'Severo',
            'relaciones_sociales' => 'Desvinculación progresiva con amigos',
            'redes_sociales' => 'Uso excesivo y aislamiento',
            'ansiedad_panico' => 'Palpitaciones y falta de aire en el colegio',
        ]);
        $pA1 = $crearPaciente('adolescente', $a1, 'Síntomas de ansiedad generalizada y aislamiento social',
            'Palpitaciones, insomnio y evitación escolar', 'alta', 'privado', 'Chacao', $psicologa, $estSeg);
        $group['adolescente'][] = $pA1;

        $a2 = PacienteAdolescente::create([
            'nombre' => 'Alexander', 'apellido' => 'Medina', 'fecha_nacimiento' => '2009-04-05',
            'cedula' => '30111222', 'genero' => 'Masculino',
            'institucion_educativa' => 'U.E. San José', 'nivel_educativo' => '2do año',
            'frecuencia_sintomas' => 'Semanal', 'intensidad_sintomas' => 'Moderado',
            'tiene_amigos' => 'Sí, pocos', 'actividades_grupales' => 'A veces',
            'irritabilidad_impulso' => 'Discusiones frecuentes con la madre',
        ]);
        $pA2 = $crearPaciente('adolescente', $a2, 'Dificultades conductuales familiares',
            'Bajo interés académico y conflictos en casa', 'media', 'publico', 'Libertador', $secretaria, $estSeg);
        $group['adolescente'][] = $pA2;

        $a3 = PacienteAdolescente::create([
            'nombre' => 'Camila', 'apellido' => 'Soto', 'fecha_nacimiento' => '2010-07-18',
            'cedula' => '29566778', 'genero' => 'Femenino',
            'frecuencia_sintomas' => 'Ocasional', 'intensidad_sintomas' => 'Moderado',
            'tiene_amigos' => 'Sí, varios', 'actividades_grupales' => 'Sí, frecuentemente',
            'pareja' => 'No refiere pareja',
        ]);
        $pA3 = $crearPaciente('adolescente', $a3, 'Trastorno adaptativo tras cambio de ciudad',
            'Nostalgia y tristeza intermitente desde la mudanza', 'media', 'publico', 'Sucre', $psicologa, $estEst);
        $group['adolescente'][] = $pA3;

        $a4 = PacienteAdolescente::create([
            'nombre' => 'Luis', 'apellido' => 'Campos', 'fecha_nacimiento' => '2008-02-11',
            'cedula' => '30677889', 'genero' => 'Masculino',
            'frecuencia_sintomas' => 'Semanal', 'intensidad_sintomas' => 'Severo',
            'tiene_amigos' => 'No', 'actividades_grupales' => 'Sí, frecuentemente',
            'autoimagen' => 'Baja autoestima marcada',
            'conductas_riesgo' => 'Expresiones autolesivas sin intención consumada',
        ]);
        $pA4 = $crearPaciente('adolescente', $a4, 'Baja autoestima y posibles conductas de riesgo',
            'Alteraciones del sueño y retraimiento marcado', 'urgencia', 'privado', 'Baruta', $psicologa, $estSeg);
        $group['adolescente'][] = $pA4;

        // ===== ADULTOS =====
        $d1 = PacienteAdulto::create([
            'nombre' => 'Ricardo', 'apellido' => 'Aguilar', 'cedula' => '17555667', 'fecha_nacimiento' => '1988-05-30',
            'genero' => 'Masculino', 'estado_civil' => 'Casado/a', 'telefono' => '0412-9988776',
            'ocupacion' => 'Ingeniero', 'lugar_trabajo' => 'Constructora RM',
            'frecuencia_sintomas' => 'Diario', 'intensidad_sintomas' => 'Severo',
            'consume_alcohol' => 'Ocasional', 'consume_tabaco' => 'No',
            'habitos_sueno' => 'Insomnio de conciliación',
            'sintomas_depresivos' => 'Anhedonia, tristeza sostenida, fatiga',
            'ideacion_suicida' => 'Idea pasiva de muerte sin plan',
        ]);
        $pD1id = $crearPaciente('adulto', $d1, 'Síntomas depresivos mayores',
            'Ideación pasiva de muerte, sin plan estructurado', 'urgencia', 'privado', 'Chacao', $psicologa, $estSeg);
        $group['adulto'][] = $pD1id;

        $d2 = PacienteAdulto::create([
            'nombre' => 'Ana', 'apellido' => 'Villalba', 'cedula' => '14223344', 'fecha_nacimiento' => '1992-01-15',
            'genero' => 'Femenino', 'estado_civil' => 'Soltero/a', 'telefono' => '0416-1122334',
            'ocupacion' => 'Docente',
            'consumo_sustancias' => 'Niega consumo',
            'sintomas_ansiedad' => 'Crisis de pánico en el aula',
        ]);
        $pD2id = $crearPaciente('adulto', $d2, 'Trastorno de angustia con agorafobia',
            'Episodios de pánico desde hace 6 meses', 'alta', 'publico', 'Libertador', $secretaria, $estNue);
        $group['adulto'][] = $pD2id;

        $d3 = PacienteAdulto::create([
            'nombre' => 'Óscar', 'apellido' => 'Luna', 'cedula' => '19001122', 'fecha_nacimiento' => '1980-09-08',
            'genero' => 'Masculino', 'estado_civil' => 'Divorciado/a', 'telefono' => '0424-5566778',
            'ocupacion' => 'Chofer',
            'consume_alcohol' => 'Frecuente',
            'habitos_sueno' => 'Somnolencia diurna',
            'sintomas_ansiedad' => 'Crisis de ansiedad en el trabajo',
            'consumo_sustancias' => 'Consumo de alcohol casi a diario',
        ]);
        $pD3 = $crearPaciente('adulto', $d3, 'Ansiedad laboral y consumo de alcohol',
            'Se descarta hipótesis de abuso de sustancias', 'media', 'publico', 'Sucre', $psicologa, $estSeg);
        $group['adulto'][] = $pD3;

        $d4 = PacienteAdulto::create([
            'nombre' => 'Elena', 'apellido' => 'Navarro', 'cedula' => '12345678', 'fecha_nacimiento' => '1995-12-03',
            'genero' => 'Femenino', 'estado_civil' => 'Unión Libre', 'telefono' => '0412-3344556',
            'ocupacion' => 'Diseñadora',
            'estado_animo' => 'Tristeza sostenida, llanto frecuente',
            'consume_tabaco' => 'Frecuente', 'otras_sustancias' => 'Cannabis ocasional',
            'habitos_sueno' => 'Insomnio de mantenimiento',
        ]);
        $pD4 = $crearPaciente('adulto', $d4, 'Duelo no resuelto por pérdida de familiar',
            'Dificultad para dormir e irritabilidad', 'media', 'privado', 'Chacao', $psicologa, $estEst);
        $group['adulto'][] = $pD4;

        $this->command->info('  - pacientes: ' . collect($group)->flatten()->count());

        return $group;
    }

    private function sembrarAcompanantes(array $pacientes): array
    {
        $datos = [
            [$pacientes['nino'][0], 'Pedro García (padre)', 'Padre', '0412-1112233', 'V-12345678', true],
            [$pacientes['nino'][0], 'Laura García (madre)', 'Madre', '0412-1112234', 'V-87654321', false],
            [$pacientes['nino'][1], 'Marta Pérez (madre)', 'Madre', '0414-2223344', 'V-23456789', true],
            [$pacientes['adolescente'][0], 'María Rojas (madre)', 'Madre', '0412-5556677', 'V-34567890', true],
            [$pacientes['adolescente'][1], 'Carlos Medina (padre)', 'Padre', '0412-6667788', 'V-45678901', true],
            [$pacientes['adolescente'][3], 'Rosa Campos (madre)', 'Madre', '0424-7778899', 'V-56789012', true],
        ];

        $out = [];
        foreach ($datos as [$paciente, $nombre, $parentesco, $tel, $cedula, $principal]) {
            $out[] = Acompanante::create([
                'paciente_id' => $paciente->id,
                'nombre' => $nombre,
                'parentesco' => $parentesco,
                'telefono' => $tel,
                'cedula' => $cedula,
                'es_principal' => $principal,
            ]);
        }
        $this->command->info('  - acompañantes: ' . count($out));
        return $out;
    }

    private function sembrarDiarios(array $pacientes): array
    {
        // Entradas escritas por los propios pacientes (en primera persona).
        // Formato: [paciente, fecha, contenido]
        $entradas = [
            [$pacientes['nino'][0], now()->subDays(6), 'Hoy me costó quedarme quieto en clase y me puse nervioso cuando la maestra me llamó. En el recreo jugué fútbol y me sentí bien.'],
            [$pacientes['nino'][0], now()->subDays(2), 'Hice casi toda la tarea y papá me felicitó. Me costó concentrarme pero lo logré.'],
            [$pacientes['nino'][1], now()->subDays(8), 'Me dio miedo ir al colegio otra vez y me dolía la barriga en la mañana. Mi mamá me acompañó y me sentí mejor después.'],
            [$pacientes['nino'][1], now()->subDays(3), 'Hoy estuve más tranquila en clase. Jugué con mi compañera nueva y no me dolió la barriga.'],
            [$pacientes['nino'][2], now()->subDays(5), 'Me enojé porque no quería ordenar mis juguetes y grité. Después me tranquilicé y jugué con mi hermana.'],
            [$pacientes['nino'][3], now()->subDays(7), 'Estoy un poco triste porque extraño cuando papá vivía con nosotros. Dibujé para sentirme mejor.'],

            [$pacientes['adolescente'][0], now()->subDays(9), 'Hoy tuve palpitaciones antes de pasar al frente en el liceo. Usé la respiración que practicamos y aunque me temblaban las manos pude hacerlo.'],
            [$pacientes['adolescente'][0], now()->subDays(2), 'Me siento ansiosa por las evaluaciones y casi no dormí. Quedarme en casa sería más fácil, pero fui al colegio.'],
            [$pacientes['adolescente'][1], now()->subDays(4), 'Discutí otra vez con mi mamá por el celular. Sé que me descontrolo, pero siento que ella no me escucha.'],
            [$pacientes['adolescente'][2], now()->subDays(6), 'Extraño mi ciudad y a mis amigas de antes. Aquí todavía me siento sola, aunque estoy intentando conocer gente.'],
            [$pacientes['adolescente'][3], now()->subDays(3), 'Hoy fue un día difícil y me sentí mal conmigo mismo. Escribir me ayuda un poco. Mañana quiero hablar con la psicóloga.'],

            [$pacientes['adulto'][0], now()->subDays(5), 'He dormido mal casi toda la semana y me cuesta verle sentido a las cosas. Aun así hoy logré salir a caminar.'],
            [$pacientes['adulto'][0], now()->subDays(1), 'Pensé otra vez en no querer estar. Se lo dije a mi esposa y me quedo con el compromiso de pedir ayuda.'],
            [$pacientes['adulto'][1], now()->subDays(4), 'Hoy tomé el metro sola por primera vez en meses. Sentí mucha ansiedad pero no tuve ninguna crisis. Estoy contenta conmigo misma.'],
            [$pacientes['adulto'][2], now()->subDays(3), 'Reconozco que el alcohol se me está yendo de las manos. Estoy dispuesto a intentar reducirlo. Hoy no bebí.'],
            [$pacientes['adulto'][3], now()->subDays(6), 'El aniversario de la pérdida de mi mamá me pesó mucho. Lloré, pero pude recordarla con cariño y dormí un poco mejor.'],
        ];

        $out = [];
        foreach ($entradas as [$pac, $fecha, $contenido]) {
            $cuenta = $this->cuentasPortal[$pac->id] ?? $pac->user;
            if (!$cuenta) {
                continue;
            }
            $d = Diario::create([
                'user_id' => $cuenta->id,
                'fecha' => $fecha->toDateString(),
                'contenido' => $contenido,
            ]);
            $this->fechar($d, $fecha);
            $out[$pac->id][] = $d;
        }
        $this->command->info('  - diarios: ' . collect($out)->flatten()->count());
        return $out;
    }

    private function sembrarCitas(array $pacientes): array
    {
        $grupos = $pacientes;
        $todas = collect($grupos)->flatten();

        $defs = [
            // [paciente, fecha, estado, objetivo, planificacion, motivoCancelacion]
            [$todas[0], now()->addDays(2), 'pendiente', 'Valoración inicial', 'Evaluación psicométrica WISC-V', null],
            [$todas[4], now()->addDays(3), 'pendiente', 'Seguimiento de ansiedad', 'Revisión de escalas de ansiedad', null],
            [$todas[8], now()->addDays(4), 'pendiente', 'Sesión de apoyo', 'Trabajo de autopercepción', null],
            [$todas[9], now()->addDays(5), 'pendiente', 'Valoración inicial', 'Anamnesis y entrevista clínica', null],
            [$todas[1], now()->addDays(7), 'pendiente', 'Terapia conductual', 'Plan de reforzamiento en aula', null],
            [$todas[6], now()->addDays(9), 'pendiente', 'Seguimiento', 'Exploración de red de apoyo', null],
            [$todas[2], now()->addDays(12), 'pendiente', 'Orientación a padres', 'Pautas de límites y rutinas', null],
            [$todas[8], now()->subDays(40), 'atendida', 'Valoración inicial', 'Entrevista clínica estructurada', null],
            [$todas[4], now()->subDays(30), 'atendida', 'Valoración inicial', 'Historia clínica y motivación de cambio', null],
            [$todas[9], now()->subDays(25), 'atendida', 'Valoración inicial', 'Crisis de pánico: historia y escalas', null],
            [$todas[1], now()->subDays(20), 'atendida', 'Sesión inicial', 'Evaluación evolutiva', null],
            [$todas[3], now()->subDays(12), 'atendida', 'Seguimiento', 'Técnicas de relajación', null],
            [$todas[0], now()->subDays(18), 'cancelada', 'Valoración', 'Reprogramada por solicitud del padre', 'Padre solicitó reprogramar'],
            [$todas[7], now()->subDays(15), 'cancelada', 'Seguimiento', null, 'Conflicto de horario laboral'],
            [$todas[2], now()->subDays(9), 'no_asistio', 'Primera consulta', 'La paciente no asistió', null],
            [$todas[10], now()->subDays(6), 'no_asistio', 'Sesión 2', null, 'No asistió'],
        ];

        $out = [];
        foreach ($defs as [$pac, $fecha, $estado, $obj, $plan, $motivo]) {
            $c = Cita::create([
                'paciente_id' => $pac->id,
                'fecha' => $fecha->toDateString(),
                'estado' => $estado,
                'objetivo' => $obj,
                'planificacion' => $plan,
                'motivo_cancelacion' => $motivo,
            ]);
            $this->fechar($c, $estado === 'pendiente' ? $fecha : $fecha->subHours(6));
            $out[] = $c;
        }
        $this->command->info('  - citas: ' . count($out));
        return $out;
    }

    private function sembrarDiagnosticos(array $pacientes, array $users): void
    {
        $psi = $users['psicologa'];
        $defs = [
            [$pacientes['nino'][0], 'F90.0', 'Trastorno por déficit de atención con hiperactividad', true, now()->subDays(15)],
            [$pacientes['nino'][0], 'F98.8', 'Otros trastornos emocionales de la infancia (oposición)', false, now()->subDays(15)],
            [$pacientes['adolescente'][0], 'F41.1', 'Trastorno de ansiedad generalizada', true, now()->subDays(28)],
            [$pacientes['adolescente'][3], 'F43.2', 'Trastorno de adaptación con alteración del comportamiento', true, now()->subDays(8)],
            [$pacientes['adulto'][0], 'F32.1', 'Episodio depresivo moderado', true, now()->subDays(32)],
            [$pacientes['adulto'][0], 'F41.0', 'Trastorno de angustia', false, now()->subDays(30)],
            [$pacientes['adulto'][1], 'F40.01', 'Agorafobia con trastorno de angustia', true, now()->subDays(24)],
            [$pacientes['adulto'][2], 'F10.2', 'Dependencia del alcohol', true, now()->subDays(10)],
            [$pacientes['adulto'][3], 'F43.2', 'Trastorno de adaptación (duelo complicado)', true, now()->subDays(18)],
        ];
        $out = 0;
        foreach ($defs as [$pac, $codigo, $desc, $principal, $fecha]) {
            Diagnostico::create([
                'paciente_id' => $pac->id,
                'user_id' => $psi->id,
                'codigo_cie' => $codigo,
                'diagnostico' => $desc,
                'es_principal' => $principal,
                'fecha' => $fecha->toDateString(),
            ]);
            $out++;
        }
        $this->command->info('  - diagnósticos: ' . $out);
    }

    private function sembrarNotas(array $pacientes, array $users, array $diarios): void
    {
        $psi = $users['psicologa'];
        $primerDiario = function ($pac) use ($diarios) {
            return $diarios[$pac->id][0] ?? null;
        };
        $defs = [
            [$pacientes['nino'][0], 'Se entregó guía de rutinas diarias. Padres observaron mejoría en tareas escolares.'],
            [$pacientes['nino'][1], 'Se solicitará reporte académico al colegio.'],
            [$pacientes['adolescente'][0], 'Escala HADS: 12/21 en ansiedad. Seguimiento semanal.'],
            [$pacientes['adolescente'][3], 'Acordado protocolo de seguridad ante riesgo autolesivo.'],
            [$pacientes['adulto'][0], 'Chequeo de ideación: pasiva, sin plan. Compromiso de no daño firmado.'],
            [$pacientes['adulto'][1], 'Refirió remisión de crisis agudas tras exposición gradual.'],
            [$pacientes['adulto'][2], 'Derivación a evaluación por medicina para estudio de consumo.'],
            [$pacientes['adulto'][3], 'Trabajo de elaboración del duelo mediante carta sin enviar.'],
        ];
        $out = 0;
        foreach ($defs as [$pac, $texto]) {
            Nota::create([
                'paciente_id' => $pac->id,
                'user_id' => $psi->id,
                'anotacion' => $texto,
                'diario_id' => $primerDiario($pac)?->id,
            ]);
            $out++;
        }
        $this->command->info('  - notas: ' . $out);
    }

    private function sembrarPlanes(array $pacientes, array $users): array
    {
        $psi = $users['psicologa'];
        $defs = [
            [$pacientes['nino'][0], 'Plan conductual para TDAH', 'Mejorar atención sostenida y autorregulación', 'activo', now()->subDays(10), now()->addMonths(2), null, 'Intervención combinada con entrenamiento a padres'],
            [$pacientes['adolescente'][0], 'Plan de regulación emocional', 'Reducir crisis de ansiedad y recuperar asistencia escolar', 'activo', now()->subDays(20), now()->addMonths(3), null, 'TCC + técnicas de relajación'],
            [$pacientes['adulto'][0], 'Plan para episodio depresivo', 'Disminuir sintomatología depresiva y riesgo', 'activo', now()->subDays(25), now()->addMonths(4), null, 'TCC + psicoeducación familiar'],
            [$pacientes['adulto'][1], 'Plan de exposición para agorafobia', 'Manejo de pánico y exposición gradual', 'completado', now()->subMonths(4), now()->subMonths(1), now()->subWeeks(1), 'Alta con mantenimiento mensual'],
            [$pacientes['nino'][3], 'Plan de acompañamiento emocional', 'Trabajo de elaboración emocional por duelo parental', 'pausado', now()->subDays(30), now()->addMonths(1), null, 'En pausa por período vacacional'],
            [$pacientes['adulto'][3], 'Plan para duelo complicado', 'Elaboración del duelo y estabilización del ánimo', 'activo', now()->subDays(15), now()->addMonths(3), null, 'Terapia de duelo'],
        ];

        $out = [];
        foreach ($defs as [$pac, $titulo, $objetivo, $estado, $ini, $finEst, $finReal, $obs]) {
            $out[] = PlanTratamiento::create([
                'paciente_id' => $pac->id,
                'user_id' => $psi->id,
                'titulo' => $titulo,
                'objetivo_general' => $objetivo,
                'estado' => $estado,
                'fecha_inicio' => $ini->toDateString(),
                'fecha_fin_estimada' => $finEst->toDateString(),
                'fecha_fin_real' => $finReal?->toDateString(),
                'observaciones' => $obs,
            ]);
        }
        $this->command->info('  - planes: ' . count($out));
        return $out;
    }

    private function sembrarObjetivos(array $planes): void
    {
        $defs = [
            [$planes[0], 'Identificar señales de distracción', 'Diario de registro de atención', 'en_progreso', now()->addDays(30), 1],
            [$planes[0], 'Reducir interrupciones en clase', 'Reporte docente bimestral', 'pendiente', now()->addDays(45), 2],
            [$planes[0], 'Fortalecer autorregulación', 'Técnicas de respiración en crisis', 'pendiente', now()->addDays(60), 3],
            [$planes[1], 'Reconocer síntomas de ansiedad', 'Autoregistro semanal de crisis', 'en_progreso', now()->addDays(20), 1],
            [$planes[1], 'Retomar asistencia escolar regular', '>80% de asistencia mensual', 'pendiente', now()->addDays(45), 2],
            [$planes[2], 'Disminuir síntomas depresivos', 'Reducción de ≥50% en PHQ-9', 'en_progreso', now()->addDays(40), 1],
            [$planes[2], 'Establecer red de apoyo', 'Sesión familiar programada', 'pendiente', now()->addDays(60), 2],
            [$planes[3], 'Manejar crisis de pánico', 'Técnica de reestructuración aplicada', 'cumplido', now()->subMonths(2), 1],
            [$planes[3], 'Exposición gradual a espacios', 'Salidas supervisadas semanales', 'cumplido', now()->subWeeks(2), 2],
            [$planes[5], 'Elaborar duelo', 'Ritual de despedida realizado', 'en_progreso', now()->addDays(30), 1],
            [$planes[5], 'Estabilizar sueño', 'Higiene de sueño consolidada', 'pendiente', now()->addDays(20), 2],
        ];
        $out = 0;
        foreach ($defs as [$plan, $desc, $meta, $estado, $limite, $orden]) {
            PlanObjetivo::create([
                'plan_id' => $plan->id,
                'descripcion' => $desc,
                'meta' => $meta,
                'estado' => $estado,
                'fecha_limite' => $limite->toDateString(),
                'orden' => $orden,
            ]);
            $out++;
        }
        $this->command->info('  - objetivos: ' . $out);
    }

    private function sembrarSesiones(array $pacientes, array $citas, array $acompanantes, array $users): void
    {
        $psi = $users['psicologa'];
        $porId = function ($list) {
            return collect($list)->keyBy('id');
        };
        $acomCita = $porId($acompanantes);

        $atendidas = array_values(array_filter($citas, fn ($c) => $c->estado === 'atendida'));

        $defs = [
            [$atendidas[0] ?? null, $pacientes['adulto'][3], null, now()->subDays(39), 45, 'Primera entrevista clínica completa; se evalúa motivación.', 'Se aplicaron escalas iniciales.'],
            [$atendidas[1] ?? null, $pacientes['adolescente'][0], null, now()->subDays(29), 50, 'Historia de ansiedad; se inicia psicoeducación sobre TCC.', 'Riesgo bajo de autoagresión.'],
            [$atendidas[2] ?? null, $pacientes['adulto'][1], null, now()->subDays(24), 50, 'Exploración de crisis de pánico; primer ejercicio de exposición imaginaria.', 'Logró completar técnica.'],
            [null, $pacientes['nino'][0], $acomCita->get($acompanantes[0]->id ?? -1), now()->subDays(8), 45, 'Juego terapéutico; entrenamiento a padres para rutinas.', 'Buena receptividad.'],
            [null, $pacientes['adolescente'][0], null, now()->subDays(5), 45, 'Escala de ansiedad; técnica de respiración diafragmática.', 'Confirma mejoría parcial.'],
            [null, $pacientes['nino'][1], $acomCita->get($acompanantes[1]->id ?? -1), now()->subDays(12), 40, 'Actividad de vínculo madre-hija; se observa ansiedad de separación.', 'Padre colaboró en dinámica.'],
            [null, $pacientes['adulto'][0], null, now()->subDays(9), 50, 'Psicoterapia cognitiva; cuestionamiento de autodescalificaciones.', 'Se mantiene red de apoyo.'],
            [null, $pacientes['adulto'][2], null, now()->subDays(7), 50, 'Psicoeducación sobre dependencia del alcohol; compromiso de reducción.', 'Derivado a valoración médica.'],
            [null, $pacientes['adolescente'][3], null, now()->subDays(4), 50, 'Plan de seguridad ante ideación; vínculo con tutor.', 'Se acuerda acompañamiento cercano.'],
            [$atendidas[3] ?? null, $pacientes['nino'][1], $acomCita->get($acompanantes[2]->id ?? -1), now()->subDays(19), 40, 'Evaluación evolutiva e inicio de seguimiento conductual.', 'Madre refiere mejoría en sueño.'],
        ];

        $out = 0;
        foreach ($defs as [$cita, $pac, $acomp, $fecha, $dur, $resumen, $obs]) {
            Sesion::create([
                'cita_id' => $cita?->id,
                'paciente_id' => $pac->id,
                'acompanante_id' => $acomp?->id ?? null,
                'user_id' => $psi->id,
                'fecha' => $fecha->toDateString(),
                'duracion_minutos' => $dur,
                'resumen' => $resumen,
                'observaciones_clinicas' => $obs,
            ]);
            $out++;
        }
        $this->command->info('  - sesiones: ' . $out);
    }

    private function sembrarNotificaciones(array $users): void
    {
        $psi = $users['psicologa'];
        $sec = $users['secretaria'];
        $admin = $users['admin'];

        $defs = [
            [$psi, 'cita_proxima', 'Cita próxima', 'Hoy tiene la cita de Mateo García a las 9:00 a.m.', now()->subHours(3), false],
            [$psi, 'recordatorio', 'Recordatorio de plan', 'El plan de Daniela Rojas cumple 1 mes en seguimiento.', now()->subHours(20), false],
            [$psi, 'sistema', 'Respaldos', 'Se creó un respaldo automático de la base de datos.', now()->subDays(1), true],
            [$sec, 'cita_confirmada', 'Cita confirmada', 'La cita de Ricardo Aguilar fue confirmada.', now()->subHours(5), false],
            [$sec, 'sistema', 'Paciente nuevo', 'Se registraron 3 pacientes nuevos esta semana.', now()->subDays(2), true],
            [$admin ?: $psi, 'sistema', 'Revisión solicitada', 'La psicóloga solicitó revisión del módulo de reportes.', now()->subHours(1), false],
            [$psi, 'session', 'Sesión registrada', 'Se registró la sesión de Ana Villalba.', now()->subHours(6), true],
        ];
        $out = 0;
        foreach ($defs as [$user, $tipo, $titulo, $msg, $fh, $leida]) {
            Notificacion::create([
                'user_id' => $user->id,
                'tipo' => $tipo,
                'titulo' => $titulo,
                'mensaje' => $msg,
                'datos' => null,
                'leida' => $leida,
                'fecha_hora' => $fh,
            ]);
            $out++;
        }
        $this->command->info('  - notificaciones: ' . $out);
    }

    private function sembrarActividadPortal(array $pacientes): void
    {
        $defs = [];
        foreach (collect($pacientes)->flatten() as $pac) {
            $cuenta = $this->cuentasPortal[$pac->id] ?? $pac->user;
            if (!$cuenta) {
                continue;
            }
            $defs[] = [$cuenta, 'inicio_sesion', 'El paciente inició sesión en el portal', now()->subHours(2)];
            $defs[] = [$cuenta, 'perfil', 'Consultó sus datos personales', now()->subHours(1)];
            $defs[] = [$cuenta, 'cita', 'Visualizó su próxima cita', now()->subHours(1)];
        }
        $out = 0;
        foreach ($defs as [$user, $accion, $desc, $fh]) {
            PatientActivityLog::create([
                'user_id' => $user->id,
                'accion' => $accion,
                'descripcion' => $desc,
                'ip' => '192.168.0.10',
                'fecha_hora' => $fh,
            ]);
            $out++;
        }
        $this->command->info('  - actividad portal: ' . $out);
    }

    private function sembrarBitacora(array $users, array $pacientes): void
    {
        $psi = $users['psicologa'];
        $sec = $users['secretaria'];

        // algunos registros de las altas creadas
        $altas = collect($pacientes)->flatten()->take(6);

        $out = 0;
        foreach ($altas as $i => $pac) {
            $autor = $i % 2 === 0 ? $psi : $sec;
            Bitacora::create([
                'usuario_id' => $autor->id,
                'usuario_nombre' => $autor->name,
                'accion' => 'CREADO',
                'tabla_afectada' => 'pacientes',
                'registro_id' => $pac->id,
                'datos_viejos' => null,
                'datos_nuevos' => json_encode(['tipo_paciente' => $pac->tipo_paciente, 'numero_expediente' => $pac->numero_expediente]),
                'ip' => '127.0.0.1',
                'fecha_hora' => now()->subDays(30)->addDays($i),
            ]);
            $out++;
        }
        $this->command->info('  - bitácora: ' . $out);
    }
}