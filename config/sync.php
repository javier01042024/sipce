<?php

return [

    /*
    | Servidor con el que sincroniza el escritorio (la API/Sanctum del SIPCE Web).
    | En el paquete desktop se apunta a la instancia en la nube.
    */
    'server_url' => rtrim((string) env('SYNC_API_URL', env('APP_URL', 'http://127.0.0.1:8899')), '/'),

    /*
    | Credenciales usadas por el dispositivo para autenticarse contra la API.
    | El bcrypt de estos usuarios vive en la BD local (permite login offline).
    */
    'email' => env('SYNC_EMAIL', 'admin@example.com'),
    'password' => env('SYNC_PASSWORD', 'password123'),
    'device_name' => env('SYNC_DEVICE_NAME', 'sipce-desktop'),

    /*
    | Identificador del dispositivo. Si no viene en el .env se genera una vez
    | y se persiste en storage/app/sync/device-id.txt.
    */
    'device_id' => env('SYNC_DEVICE_ID'),

    'http_timeout' => (int) env('SYNC_HTTP_TIMEOUT', 12),
    'upload_chunk' => (int) env('SYNC_UPLOAD_CHUNK', 50),

    /*
    | Tablas sincronizables (el orden = orden de aplicación en download/upload;
    | las dependencias van antes que los registros que las referencian).
    | Clave (contrato de sync) => [tabla física, modelo].
    */
    'tables' => [
        'estados'              => ['estados', \App\Models\Estado::class],
        'paciente_adultos'     => ['paciente_adultos', \App\Models\PacienteAdulto::class],
        'paciente_adolescentes'=> ['paciente_adolescentes', \App\Models\PacienteAdolescente::class],
        'paciente_ninos'       => ['paciente_ninos', \App\Models\PacienteNino::class],
        'pacientes'            => ['pacientes', \App\Models\Paciente::class],
        'diarios'              => ['diarios', \App\Models\Diario::class],
        'citas'                => ['citas', \App\Models\Cita::class],
        'acompanantes'         => ['acompanantes', \App\Models\Acompanante::class],
        'diagnosticos'         => ['diagnosticos', \App\Models\Diagnostico::class],
        'plan_tratamiento'     => ['planes_tratamiento', \App\Models\PlanTratamiento::class],
        'sesiones'             => ['sesiones', \App\Models\Sesion::class],
        'plan_objetivos'       => ['plan_objetivos', \App\Models\PlanObjetivo::class],
        'notas'                => ['notas', \App\Models\Nota::class],
        'notificaciones'       => ['notificaciones', \App\Models\Notificacion::class],
    ],

    /*
    | Traducción clave-sync => tabla física para reverse lookup del observer.
    */
    'physical_tables' => [
        'estados' => 'estados',
        'paciente_adultos' => 'paciente_adultos',
        'paciente_adolescentes' => 'paciente_adolescentes',
        'paciente_ninos' => 'paciente_ninos',
        'pacientes' => 'pacientes',
        'diarios' => 'diarios',
        'citas' => 'citas',
        'acompanantes' => 'acompanantes',
        'diagnosticos' => 'diagnosticos',
        'planes_tratamiento' => 'plan_tratamiento',
        'sesiones' => 'sesiones',
        'plan_objetivos' => 'plan_objetivos',
        'notas' => 'notas',
        'notificaciones' => 'notificaciones',
    ],

    /*
    | Campos de clave foránea que hay que convertir id<->uuid.
    | Valor = clave de tabla de sync destino ('detalle' se resuelve por tipo).
    'user_id' no se sincroniza como tabla pero sí como FK → usuarios.
    */
    'fk_fields' => [
        'paciente_id'        => 'pacientes',
        'paciente_detalle_id'=> '',
        'cita_id'            => 'citas',
        'acompanante_id'     => 'acompanantes',
        'diario_id'          => 'diarios',
        'plan_id'            => 'plan_tratamiento',
        'estado_id'          => 'estados',
        'user_id'            => 'users',
    ],

    /*
    | Tipo polimórfico paciente_detalle_type => clave de tabla de sync.
    */
    'detalle_map' => [
        'App\\Models\\PacienteAdulto'       => 'paciente_adultos',
        'App\\Models\\PacienteAdolescente'  => 'paciente_adolescentes',
        'App\\Models\\PacienteNino'         => 'paciente_ninos',
    ],
];