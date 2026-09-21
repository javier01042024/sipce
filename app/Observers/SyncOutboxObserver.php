<?php

namespace App\Observers;

use App\Models\SyncPending;
use App\Services\SyncService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Registra en la cola local (sync_pending) cada cambio hecho en una tabla
 * sincronizable. Solo se registra cuando la app corre en modo desktop
 * (env SYNC_OUTBOX=true). Los cambios aplicados por el propio sync se
 * ignoran gracias a SyncService::$busy.
 */
class SyncOutboxObserver
{
    public function created(Model $model): void
    {
        $this->push($model, 'CREATE');
    }

    public function updated(Model $model): void
    {
        $this->push($model, 'UPDATE');
    }

    public function deleted(Model $model): void
    {
        $this->push($model, 'DELETE');
    }

    private function push(Model $model, string $accion): void
    {
        if (SyncService::$busy) {
            return;
        }

        $tablaKey = $this->tablaKeyFor($model->getTable());
        if (! $tablaKey) {
            return;
        }

        $uuid = $model->uuid ?: (string) Str::uuid();

        $userId = Auth::id();
        if (! $userId) {
            // Contexto de consola/script: usar la cuenta de sync local
            $userId = \App\Models\User::where('email', config('sync.email', 'admin@example.com'))->value('id');
        }

        // La cola guarda UNA fila por uuid (unique): el último estado no sincronizado.
        // Si el registro ya se sincronizó, la misma fila vuelve a estar pendiente.
        SyncPending::updateOrCreate(
            ['uuid' => $uuid],
            [
                'tabla' => $tablaKey,
                'accion' => $accion,
                'datos' => $model->getAttributes(),
                'device_id' => (string) config('sync.device_id', 'desktop'),
                'user_id' => $userId,
                'created_local' => now(),
                'sincronizado' => false,
                'sincronizado_at' => null,
                'error_sync' => null,
            ]
        );
    }

    private function tablaKeyFor(string $table): ?string
    {
        return config('sync.physical_tables')[$table] ?? null;
    }
}