<?php

namespace App\Services;

use App\Models\SyncPending;
use App\Models\SyncState;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncService
{
    /**
     * El observer de outbox comprueba esta bandera para no re-registrar
     * los cambios que nosotros mismos aplicamos durante el download.
     */
    public static bool $busy = false;

    private string $baseUrl;
    private string $deviceId;
    private string $token;
    private string $storageDir;
    private mixed $lockHandle = null;

    public function __construct()
    {
        $this->baseUrl = (string) config('sync.server_url');
        $this->storageDir = storage_path('app/sync');
        $this->deviceId = $this->resolveDeviceId();
        $this->token = $this->readToken();
    }

    /* ======================================================================
     |  ORQUESTACIÓN
     | ====================================================================== */

    /**
     * Ejecuta un ciclo completo de sincronización.
     * @return array{status:string, ...}
     */
    public function run(): array
    {
        if ($this->isSameOriginServer()) {
            return ['status' => 'disabled', 'message' => 'El escritorio no sincroniza contra sí mismo.'];
        }

        if (! $this->acquireLock()) {
            return ['status' => 'busy'];
        }

        self::$busy = true;

        try {
            if (! $this->online()) {
                return ['status' => 'offline'];
            }

            $boot = $this->bootstrap();
            $up = $this->upload();
            $down = $this->download();

            return [
                'status' => 'ok',
                'bootstrapped' => $boot,
                'uploaded' => $up['uploaded'],
                'conflicts' => $up['conflicts'],
                'download_ok' => $down['ok'],
                'downloaded' => $down['downloaded'],
                'pending' => SyncPending::where('sincronizado', false)->count(),
            ];
        } finally {
            self::$busy = false;
            $this->releaseLock();
        }
    }

    /* ======================================================================
     |  BOOTSTRAP (usuarios, roles, mapeo id -> uuid)
     | ====================================================================== */

    private function bootstrap(): bool
    {
        $resp = $this->withAuth(fn () => Http::withToken($this->token)
            ->timeout(config('sync.http_timeout'))
            ->acceptJson()
            ->get($this->baseUrl.'/api/sync/bootstrap'));

        if (! $resp->successful()) {
            return false;
        }

        $json = $resp->json();
        $roles = $json['roles'] ?? [];
        $users = $json['users'] ?? [];
        $roleUser = $json['role_user'] ?? [];

        $this->ensureRemapTable();

        // ---- roles: upsert por slug (mantener ids locales estables) ----
        foreach ($roles as $r) {
            $role = \App\Models\Role::where('slug', $r['slug'])->first() ?? new \App\Models\Role();
            $role->timestamps = false;
            $role->forceFill([
                'name' => $r['name'],
                'slug' => $r['slug'],
                'description' => $r['description'] ?? null,
                'permissions' => $r['permissions'] ?? [],
            ])->save();
        }

        // ---- usuarios: upsert por email, respaldado por uuid ----
        foreach ($users as $u) {
            $user = User::where('email', $u['email'])->first()
                ?? (($u['uuid'] ?? null) ? User::where('uuid', $u['uuid'])->first() : null)
                ?? new User();

            $datos = $u;
            unset($datos['id']);
            $datos['uuid'] = $datos['uuid'] ?? (string) Str::uuid();
            $user->timestamps = false;
            $user->forceFill($datos)->save();

            $this->remapUpsert('users', (int) $u['id'], $user->uuid, $user->id);
        }

        // ---- rol_usuario: recargar asignaciones ----
        $rolesLocal = \App\Models\Role::pluck('id', 'slug');
        foreach ($users as $u) {
            $localUser = User::where('email', $u['email'])->first();
            if (! $localUser) {
                continue;
            }
            $slugs = collect($roleUser)
                ->where('user_id', $u['id'])
                ->pluck('role_id');
            $roleIds = collect($roles)
                ->whereIn('id', $slugs)
                ->pluck('slug')
                ->map(fn ($slug) => $rolesLocal[$slug] ?? null)
                ->filter()
                ->all();
            $localUser->roles()->sync($roleIds);
        }

        return true;
    }

    /* ======================================================================
     |  UPLOAD (cola local -> servidor)
     | ====================================================================== */

    private function upload(): array
    {
        $chunk = (int) config('sync.upload_chunk');
        $pending = SyncPending::where('sincronizado', false)
            ->orderBy('id')
            ->limit($chunk)
            ->get();

        if ($pending->isEmpty()) {
            return ['uploaded' => 0, 'conflicts' => 0];
        }

        $order = array_flip(array_keys(config('sync.tables')));

        $records = $pending->map(function (SyncPending $p) use ($order) {
            return [
                'uuid' => $p->uuid,
                'tabla' => $p->tabla,
                'accion' => $p->accion,
                'datos' => $this->fkToUuid($p->datos ?? [], $p->tabla),
                'created_local' => $p->created_local?->toIso8601String() ?? now()->toIso8601String(),
            ];
        })->values()->all();

        usort($records, function ($a, $b) use ($order) {
            return ($order[$a['tabla']] ?? 999) <=> ($order[$b['tabla']] ?? 999);
        });

        $resp = $this->withAuth(fn () => Http::withToken($this->token)
            ->timeout(config('sync.http_timeout'))
            ->asJson()
            ->acceptJson()
            ->post($this->baseUrl.'/api/sync/upload', [
                'device_id' => $this->deviceId,
                'records' => $records,
            ]));

        if (! $resp->successful()) {
            Log::warning('Sync upload HTTP error', ['status' => $resp->status(), 'body' => substr((string) $resp->body(), 0, 500)]);
            return ['uploaded' => 0, 'conflicts' => 0];
        }

        $json = $resp->json();
        $detalle = $json['detalle'] ?? [];
        $conflicts = 0;

        if ($detalle) {
            foreach ($detalle as $d) {
                $uuid = $d['uuid'] ?? null;
                if (! $uuid) {
                    continue;
                }
                $marcar = match ($d['estado'] ?? 'error') {
                    'applied' => ['sincronizado' => true, 'sincronizado_at' => now(), 'error_sync' => null],
                    'conflict' => ['sincronizado' => true, 'sincronizado_at' => now(), 'error_sync' => 'conflicto: existe una versión más reciente en el servidor'],
                    default => ['error_sync' => 'error en servidor: '.($d['mensaje'] ?? 'desconocido'), 'sincronizado' => false, 'sincronizado_at' => null],
                };
                if (($d['estado'] ?? 'error') === 'conflict') {
                    $conflicts++;
                }
                SyncPending::where('uuid', $uuid)->update($marcar);
            }
        } else {
            // servidor antiguo sin detalle: marcar todas como enviadas
            SyncPending::whereIn('uuid', $pending->pluck('uuid'))->update([
                'sincronizado' => true,
                'sincronizado_at' => now(),
            ]);
        }

        return [
            'uploaded' => count($records),
            'conflicts' => $conflicts,
        ];
    }

    /* ======================================================================
     |  DOWNLOAD (servidor -> local)
     | ====================================================================== */

    private function download(): array
    {
        $state = $this->syncState();
        $lastSync = $state?->last_sync_at?->toIso8601String();

        $resp = $this->withAuth(fn () => Http::withToken($this->token)
            ->timeout(config('sync.http_timeout'))
            ->acceptJson()
            ->get($this->baseUrl.'/api/sync/download', [
                'device_id' => $this->deviceId,
                'last_sync' => $lastSync,
            ]));

        if (! $resp->successful()) {
            Log::warning('Sync download HTTP error', ['status' => $resp->status()]);
            return ['ok' => false, 'downloaded' => 0];
        }

        $json = $resp->json();
        $records = $json['records'] ?? [];

        $this->ensureRemapTable();
        $this->applyDownload($records);

        $serverTime = $json['server_time'] ?? now()->toIso8601String();

        if ($state) {
            $state->update([
                'last_sync_at' => $serverTime,
                'records_received' => ($state->records_received ?? 0) + count($records),
            ]);
        }

        return ['ok' => true, 'downloaded' => count($records)];
    }

    private function applyDownload(array $records): void
    {
        // índice uuid por (tabla, servidor id) para resolver FK dentro del mismo lote
        $batchUuid = [];
        foreach ($records as $r) {
            if (isset($r['tabla'], $r['uuid'], $r['datos']['id'])) {
                $batchUuid[$r['tabla'].':'.$r['datos']['id']] = $r['uuid'];
            }
        }

        foreach ($records as $r) {
            try {
                $this->applyRecord($r, $batchUuid);
            } catch (\Throwable $e) {
                Log::warning('Sync apply record', ['uuid' => $r['uuid'] ?? null, 'tabla' => $r['tabla'] ?? null, 'error' => $e->getMessage()]);
            }
        }
    }

    private function applyRecord(array $record, array $batchUuid): void
    {
        $tablaKey = $record['tabla'] ?? null;
        if (! isset(config('sync.tables')[$tablaKey])) {
            return; // users, etc. se manejan en bootstrap
        }

        [$table, $modelClass] = config('sync.tables')[$tablaKey];
        $uuid = $record['uuid'] ?? null;
        $accion = $record['accion'] ?? 'UPDATE';
        $datos = $record['datos'] ?? [];
        $serverId = $datos['id'] ?? null;

        $query = method_exists($modelClass, 'withTrashed')
            ? $modelClass::withTrashed()
            : $modelClass::query();

        $existing = $uuid ? $query->where('uuid', $uuid)->first() : null;

        // ---- DELETE ----
        if ($accion === 'DELETE') {
            if ($existing) {
                method_exists($existing, 'forceDelete') ? $existing->forceDelete() : $existing->delete();
            }
            if ($serverId) {
                $this->remapUpsert($tablaKey, (int) $serverId, $uuid, $existing?->id);
            }
            return;
        }

        // ---- CREATE / UPDATE ----
        if (empty($datos)) {
            return;
        }

        $datos = $this->mapFkServerToLocal($datos, $tablaKey, $batchUuid);
        unset($datos['id']);
        $datos['uuid'] = $uuid ?: ($existing?->uuid ?: (string) Str::uuid());

        $model = $existing ?: new $modelClass();
        $model->timestamps = false;
        $model->forceFill($datos)->save();

        if ($serverId) {
            $this->remapUpsert($tablaKey, (int) $serverId, $model->uuid, $model->id);
        }
    }

    /* ======================================================================
     |  CONVERSIÓN DE CLAVES FORÁNEAS
     | ====================================================================== */

    /**
     * Convertir FK numéricas locales -> uuid (para subir).
     */
    private function fkToUuid(array $datos, string $tablaKey): array
    {
        foreach (array_keys(config('sync.fk_fields')) as $campo) {
            if (empty($datos[$campo]) || ! is_numeric($datos[$campo])) {
                continue;
            }

            $targetKey = $campo === 'user_id'
                ? 'users'
                : config('sync.fk_fields')[$campo];

            if ($campo === 'paciente_detalle_id') {
                $targetKey = $this->detalleKeyFor($datos['paciente_detalle_type'] ?? '');
                if (! $targetKey) {
                    continue;
                }
                $uuid = $this->targetUuidByLocal($targetKey, (int) $datos[$campo]);
                if ($uuid) {
                    $datos[$campo] = $uuid;
                }
                continue;
            }

            $uuid = $this->targetUuidByLocal($targetKey, (int) $datos[$campo]);
            if ($uuid) {
                $datos[$campo] = $uuid;
            }
        }

        return $datos;
    }

    /**
     * Convertir FK numéricas del servidor -> id local (para aplicar download).
     */
    private function mapFkServerToLocal(array $datos, string $tablaKey, array $batchUuid): array
    {
        foreach (array_keys(config('sync.fk_fields')) as $campo) {
            if (empty($datos[$campo]) || ! is_numeric($datos[$campo])) {
                continue;
            }

            $targetKey = $campo === 'user_id'
                ? 'users'
                : config('sync.fk_fields')[$campo];

            if ($campo === 'paciente_detalle_id') {
                $targetKey = $this->detalleKeyFor($datos['paciente_detalle_type'] ?? '');
                if (! $targetKey) {
                    continue;
                }
                $localId = $this->serverIdToLocal($targetKey, (int) $datos[$campo], $batchUuid);
                if ($localId) {
                    $datos[$campo] = $localId;
                }
                continue;
            }

            $localId = $this->serverIdToLocal($targetKey, (int) $datos[$campo], $batchUuid);
            if ($localId) {
                $datos[$campo] = $localId;
            }
        }

        return $datos;
    }

    private function detalleKeyFor(string $type): ?string
    {
        $map = config('sync.detalle_map');
        if (isset($map[$type])) {
            return $map[$type];
        }
        // algunos clientes guardan el class basename
        foreach ($map as $class => $key) {
            if (str_ends_with($class, '\\'.$type) || $class === 'App\\Models\\'.$type) {
                return $key;
            }
        }
        return null;
    }

    private function targetTable(string $key): string
    {
        return $key === 'users' ? 'users' : (config('sync.tables')[$key][0] ?? $key);
    }

    private function targetUuidByLocal(string $key, int $localId): ?string
    {
        $value = DB::table($this->targetTable($key))->where('id', $localId)->value('uuid');
        return $value ? (string) $value : null;
    }

    private function serverIdToLocal(string $key, int $serverId, array $batchUuid): ?int
    {
        // 1) remap persistente almacenado en ciclos anteriores
        $row = DB::table('sync_remap')->where('tabla', $key)->where('server_id', $serverId)->first();
        if ($row) {
            return (int) $row->local_id;
        }

        // 2) el objetivo viene en este mismo lote
        $uuid = $batchUuid[$key.':'.$serverId] ?? null;
        if ($uuid) {
            $localId = DB::table($this->targetTable($key))->where('uuid', $uuid)->value('id');
            if ($localId) {
                $this->remapUpsert($key, $serverId, $uuid, (int) $localId);
                return (int) $localId;
            }
        }

        // 3) aún no disponible: no tocar (se corregirá en el próximo ciclo)
        return null;
    }

    /* ======================================================================
     |  REMAP (tabla local: tabla, server_id, uuid, local_id)
     | ====================================================================== */

    private function ensureRemapTable(): void
    {
        DB::statement(
            'CREATE TABLE IF NOT EXISTS sync_remap (tabla TEXT NOT NULL, server_id INTEGER NOT NULL, uuid TEXT NULL, local_id INTEGER NULL, PRIMARY KEY (tabla, server_id))'
        );
    }

    private function remapUpsert(string $key, int $serverId, ?string $uuid, ?int $localId): void
    {
        DB::table('sync_remap')->updateOrInsert(
            ['tabla' => $key, 'server_id' => $serverId],
            ['uuid' => $uuid, 'local_id' => $localId]
        );
    }

    /* ======================================================================
     |  AUTH / TOKEN / UTILIDADES
     | ====================================================================== */

    private function withAuth(callable $fn)
    {
        $resp = $fn();
        if ($resp->status() === 401) {
            $this->refreshToken();
            return $fn();
        }
        return $resp;
    }

    private function refreshToken(): bool
    {
        $resp = Http::timeout(config('sync.http_timeout'))
            ->asJson()
            ->acceptJson()
            ->post($this->baseUrl.'/api/login', [
                'email' => config('sync.email'),
                'password' => config('sync.password'),
                'device_name' => config('sync.device_name'),
            ]);

        if (! $resp->successful() || empty($resp->json('token'))) {
            throw new \RuntimeException('No se pudo autenticar el dispositivo contra '.$this->baseUrl);
        }

        $this->token = $resp->json('token');
        $this->saveToken($this->token);
        return true;
    }

    private function online(): bool
    {
        try {
            $resp = Http::timeout(6)->get($this->baseUrl.'/up');
            return $resp->successful();
        } catch (\Throwable) {
            return false;
        }
    }

    private function syncState(): ?SyncState
    {
        $userId = $this->localSyncUserId();
        if (! $userId) {
            return null;
        }
        return SyncState::firstOrCreate(
            ['device_id' => $this->deviceId, 'user_id' => $userId],
            ['last_sync_at' => now()]
        );
    }

    private function localSyncUserId(): ?int
    {
        return User::where('email', config('sync.email'))->value('id');
    }

    private function isSameOriginServer(): bool
    {
        $server = parse_url($this->baseUrl);
        $app = parse_url(url('/'));
        return isset($server['host'], $app['host']) && $server['host'] === $app['host'];
    }

    private function resolveDeviceId(): string
    {
        $configured = config('sync.device_id');
        if ($configured) {
            return (string) $configured;
        }

        $file = $this->storageDir.'/device-id.txt';
        if (file_exists($file)) {
            $id = trim((string) file_get_contents($file));
            if ($id !== '') {
                return $id;
            }
        }

        if (! is_dir($this->storageDir)) {
            @mkdir($this->storageDir, 0777, true);
        }

        $id = 'desktop-'.strtolower(Str::random(12));
        @file_put_contents($file, $id);
        return $id;
    }

    private function readToken(): string
    {
        $file = $this->storageDir.'/auth.json';
        if (! file_exists($file)) {
            return '';
        }
        $data = json_decode((string) file_get_contents($file), true);
        return (string) ($data['token'] ?? '');
    }

    private function saveToken(string $token): void
    {
        if (! is_dir($this->storageDir)) {
            @mkdir($this->storageDir, 0777, true);
        }
        @file_put_contents($this->storageDir.'/auth.json', json_encode(['token' => $token]));
    }

    private function acquireLock(): bool
    {
        if (! is_dir($this->storageDir)) {
            @mkdir($this->storageDir, 0777, true);
        }
        $this->lockHandle = fopen($this->storageDir.'/sync.lock', 'c');
        if (! $this->lockHandle) {
            return false;
        }
        if (! flock($this->lockHandle, LOCK_EX | LOCK_NB)) {
            return false;
        }
        return true;
    }

    private function releaseLock(): void
    {
        if ($this->lockHandle) {
            @flock($this->lockHandle, LOCK_UN);
            @fclose($this->lockHandle);
            $this->lockHandle = null;
        }
    }
}