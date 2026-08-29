<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SyncPending;
use App\Models\SyncState;
use App\Models\Paciente;
use App\Models\PacienteAdulto;
use App\Models\PacienteAdolescente;
use App\Models\PacienteNino;
use App\Models\Cita;
use App\Models\Sesion;
use App\Models\Nota;
use App\Models\Diario;
use App\Models\Diagnostico;
use App\Models\Acompanante;
use App\Models\Notificacion;
use App\Models\PlanTratamiento;
use App\Models\PlanObjetivo;
use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncController extends Controller
{
    /**
     * Tablas sincronizables y sus modelos
     */
    private array $tablas = [
        'pacientes' => Paciente::class,
        'paciente_adultos' => PacienteAdulto::class,
        'paciente_adolescentes' => PacienteAdolescente::class,
        'paciente_ninos' => PacienteNino::class,
        'citas' => Cita::class,
        'sesiones' => Sesion::class,
        'notas' => Nota::class,
        'diarios' => Diario::class,
        'diagnosticos' => Diagnostico::class,
        'acompanantes' => Acompanante::class,
        'notificaciones' => Notificacion::class,
        'plan_tratamiento' => PlanTratamiento::class,
        'plan_objetivos' => PlanObjetivo::class,
        'estados' => Estado::class,
    ];

    /**
     * POST /api/sync/upload
     * El dispositivo envía cambios realizados offline
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'device_id' => 'required|string|max:100',
            'records' => 'required|array|min:1',
            'records.*.uuid' => 'required|string',
            'records.*.tabla' => 'required|string',
            'records.*.accion' => 'required|in:CREATE,UPDATE,DELETE',
            'records.*.datos' => 'nullable|array',
            'records.*.created_local' => 'required|date',
        ]);

        $userId = $request->user()->id;
        $deviceId = $request->device_id;
        $records = $request->records;
        $applied = 0;
        $conflicts = 0;
        $errors = 0;

        DB::beginTransaction();

        try {
            foreach ($records as $record) {
                try {
                    $resultado = $this->aplicarRegistro($record, $userId, $deviceId);
                    if ($resultado === 'applied') {
                        $applied++;
                    } elseif ($resultado === 'conflict') {
                        $conflicts++;
                    }
                } catch (\Exception $e) {
                    $errors++;
                    Log::warning('Sync upload error', [
                        'uuid' => $record['uuid'],
                        'tabla' => $record['tabla'],
                        'error' => $e->getMessage(),
                    ]);

                    SyncPending::create([
                        'uuid' => $record['uuid'],
                        'tabla' => $record['tabla'],
                        'accion' => $record['accion'],
                        'datos' => $record['datos'] ?? null,
                        'device_id' => $deviceId,
                        'user_id' => $userId,
                        'created_local' => $record['created_local'],
                        'error_sync' => $e->getMessage(),
                    ]);
                }
            }

            // Actualizar estado de sincronización
            SyncState::updateOrCreate(
                ['device_id' => $deviceId, 'user_id' => $userId],
                [
                    'last_sync_at' => now(),
                    'records_sent' => DB::raw('records_sent + ' . count($records)),
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'applied' => $applied,
                'conflicts' => $conflicts,
                'errors' => $errors,
                'server_time' => now()->toIso8601String(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sync upload failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error en el servidor durante la sincronización.',
            ], 500);
        }
    }

    /**
     * GET /api/sync/download
     * El dispositivo descarga cambios del servidor desde su último sync
     */
    public function download(Request $request): JsonResponse
    {
        $request->validate([
            'device_id' => 'required|string|max:100',
            'last_sync' => 'nullable|date',
        ]);

        $userId = $request->user()->id;
        $deviceId = $request->device_id;
        $lastSync = $request->input('last_sync');

        // Si es la primera vez, devolver todo
        if (!$lastSync) {
            $syncState = SyncState::where('device_id', $deviceId)
                ->where('user_id', $userId)
                ->first();
            $lastSync = $syncState?->last_sync_at;
        }

        $records = [];

        foreach ($this->tablas as $tabla => $modelClass) {
            $query = $modelClass::withTrashed();

            if ($lastSync) {
                $query->where(function ($q) use ($lastSync) {
                    $q->where('updated_at', '>', $lastSync)
                      ->orWhere('deleted_at', '>', $lastSync);
                });
            }

            $rows = $query->get();

            foreach ($rows as $row) {
                $accion = $row->trashed() ? 'DELETE' : ($row->wasRecentlyCreated ? 'CREATE' : 'UPDATE');

                $records[] = [
                    'uuid' => $row->uuid ?? (string) Str::uuid(),
                    'tabla' => $tabla,
                    'accion' => $accion,
                    'datos' => $row->toArray(),
                    'updated_at' => $row->updated_at?->toIso8601String(),
                    'deleted_at' => $row->deleted_at?->toIso8601String(),
                ];
            }
        }

        // Actualizar estado
        SyncState::updateOrCreate(
            ['device_id' => $deviceId, 'user_id' => $userId],
            [
                'last_sync_at' => now(),
                'records_received' => count($records),
            ]
        );

        return response()->json([
            'success' => true,
            'records' => $records,
            'server_time' => now()->toIso8601String(),
            'total' => count($records),
        ]);
    }

    /**
     * GET /api/sync/status
     * Estado actual de sincronización del dispositivo
     */
    public function status(Request $request): JsonResponse
    {
        $request->validate([
            'device_id' => 'required|string|max:100',
        ]);

        $state = SyncState::where('device_id', $request->device_id)
            ->where('user_id', $request->user()->id)
            ->first();

        $pendingCount = SyncPending::where('user_id', $request->user()->id)
            ->pendientes()
            ->count();

        return response()->json([
            'success' => true,
            'last_sync_at' => $state?->last_sync_at?->toIso8601String(),
            'records_sent_total' => $state?->records_sent ?? 0,
            'records_received_total' => $state?->records_received ?? 0,
            'pending_queue' => $pendingCount,
        ]);
    }

    /**
     * POST /api/sync/resolve-conflict
     * Resolver conflicto específico manualmente
     */
    public function resolveConflict(Request $request): JsonResponse
    {
        $request->validate([
            'uuid' => 'required|string',
            'tabla' => 'required|string',
            'winner' => 'required|in:local,server',
            'datos' => 'required|array',
        ]);

        $tabla = $request->tabla;
        if (!isset($this->tablas[$tabla])) {
            return response()->json(['success' => false, 'message' => 'Tabla no válida.'], 422);
        }

        $modelClass = $this->tablas[$tabla];
        $record = $modelClass::where('uuid', $request->uuid)->first();

        if ($request->winner === 'server' && $record) {
            // Mantener servidor, no hacer nada
        } elseif ($request->winner === 'local') {
            // Sobrescribir con datos del dispositivo
            if ($record) {
                $record->update($request->datos);
            } else {
                $modelClass::create(array_merge(
                    $request->datos,
                    ['uuid' => $request->uuid]
                ));
            }
        }

        return response()->json(['success' => true, 'message' => 'Conflicto resuelto.']);
    }

    /**
     * Si pacientes requiere detalle polimórfica a una Niño/Adolescente/Adulto
     * referenciada por UUID (creadas offline), resolver a su id numérico.
     */
    private function resolverDetalle(array &$datos): void
    {
        if (empty($datos['paciente_detalle_type']) || empty($datos['paciente_detalle_id'])) {
            return;
        }

        $detalleUuid = (string) $datos['paciente_detalle_id'];

        // Ya es un id numérico
        if (ctype_digit($detalleUuid)) {
            return;
        }

        $mapa = [
            'App\\Models\\PacienteAdulto' => PacienteAdulto::class,
            'App\\Models\\PacienteAdolescente' => PacienteAdolescente::class,
            'App\\Models\\PacienteNino' => PacienteNino::class,
        ];

        $clase = $mapa[$datos['paciente_detalle_type']] ?? null;
        if (!$clase) {
            return;
        }

        $detalle = $clase::where('uuid', $detalleUuid)->withTrashed()->first();
        if ($detalle) {
            $datos['paciente_detalle_id'] = $detalle->id;
        }
    }

    /**
     * Resolver claves foráneas enviadas como UUID (creadas offline) a ids numéricos.
     */
    private function resolverIds(array &$datos): void
    {
        $mapas = [
            'paciente_id' => Paciente::class,
            'cita_id' => Cita::class,
            'acompanante_id' => Acompanante::class,
            'diario_id' => Diario::class,
            'plan_id' => PlanTratamiento::class,
            'user_id' => \App\Models\User::class,
        ];

        foreach ($mapas as $campo => $clase) {
            if (empty($datos[$campo]) || !is_string($datos[$campo])) {
                continue;
            }
            $valor = $datos[$campo];
            if (ctype_digit($valor)) {
                continue;
            }
            $obj = $clase::where('uuid', $valor)->first();
            if ($obj) {
                $datos[$campo] = $obj->id;
            }
        }
    }

    /**
     * Siguiente número de expediente EXP-000001 estilo web.
     */
    private function siguienteExpediente(): string
    {
        $ultimo = Paciente::withTrashed()->latest('id')->first();
        $numero = $ultimo ? intval(substr($ultimo->numero_expediente ?? 'EXP-000000', 4)) + 1 : 1;
        return 'EXP-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Aplicar un registro individual del upload
     */
    private function aplicarRegistro(array $record, int $userId, string $deviceId): string
    {
        $tabla = $record['tabla'];
        if (!isset($this->tablas[$tabla])) {
            throw new \Exception("Tabla no soportada: {$tabla}");
        }

        $modelClass = $this->tablas[$tabla];
        $existing = $modelClass::where('uuid', $record['uuid'])->first();

        switch ($record['accion']) {
            case 'CREATE':
                if ($existing) {
                    // Ya existe — puede ser reenvío, ignorar
                    return 'applied';
                }
                $datos = $record['datos'] ?? [];
                $datos['uuid'] = $record['uuid'];
                // Asegurar que user_id esté si aplica
                if (in_array($tabla, ['pacientes', 'sesiones', 'diarios', 'notas', 'notificaciones'])) {
                    $datos['user_id'] = $datos['user_id'] ?? $userId;
                }
                $this->resolverIds($datos);
                if ($tabla === 'pacientes') {
                    // La app móvil puede referenciar la detalle por UUID (fuera de línea)
                    $this->resolverDetalle($datos);
                    // Generar expediente automáticamente si no se envió
                    if (empty($datos['numero_expediente'])) {
                        $datos['numero_expediente'] = $this->siguienteExpediente();
                    }
                }
                $modelClass::create($datos);
                return 'applied';

            case 'UPDATE':
                if (!$existing) {
                    // No existe en servidor — crear con el UUID
                    $datos = $record['datos'] ?? [];
                    $datos['uuid'] = $record['uuid'];
                    $this->resolverIds($datos);
                    if ($tabla === 'pacientes') {
                        $this->resolverDetalle($datos);
                        if (empty($datos['numero_expediente'])) {
                            $datos['numero_expediente'] = $this->siguienteExpediente();
                        }
                    }
                    $modelClass::create($datos);
                    return 'applied';
                }
                // Verificar conflicto: ¿el servidor fue modificado después?
                $serverUpdated = $existing->updated_at;
                $clientUpdated = $record['created_local'];

                if ($serverUpdated && $serverUpdated->gt($clientUpdated)) {
                    // Conflicto — el servidor tiene datos más recientes
                    // Guardar en cola para resolución manual
                    SyncPending::create([
                        'uuid' => $record['uuid'],
                        'tabla' => $tabla,
                        'accion' => 'UPDATE',
                        'datos' => $record['datos'],
                        'device_id' => $deviceId,
                        'user_id' => $userId,
                        'created_local' => $record['created_local'],
                    ]);
                    return 'conflict';
                }
                // El cliente es más reciente — aplicar
                $aplicar = $record['datos'] ?? [];
                $this->resolverIds($aplicar);
                $existing->update($aplicar);
                return 'applied';

            case 'DELETE':
                if ($existing) {
                    if (method_exists($existing, 'forceDelete')) {
                        $existing->forceDelete();
                    } else {
                        $existing->delete();
                    }
                }
                return 'applied';

            default:
                throw new \Exception("Acción no válida: {$record['accion']}");
        }
    }
}
