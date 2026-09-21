<?php

namespace App\Http\Controllers;

use App\Models\SyncPending;
use App\Services\SyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Estado y disparo de la sincronización del escritorio desde el navegador.
 */
class SyncController extends Controller
{
    public function status(Request $request): JsonResponse
    {
        $pending = SyncPending::where('sincronizado', false)->count();
        $last = SyncPending::where('sincronizado', true)
            ->whereNotNull('sincronizado_at')
            ->orderByDesc('sincronizado_at')
            ->value('sincronizado_at');

        return response()->json([
            'online' => (bool) ($request->session()->get('_sync_online', true)),
            'syncing' => SyncService::$busy,
            'pending' => $pending,
            'last_sync' => $last ? \Illuminate\Support\Carbon::parse($last)->toIso8601String() : null,
            'server' => config('sync.server_url'),
        ]);
    }

    public function runNow(): JsonResponse
    {
        $result = app(SyncService::class)->run();

        $request = request();
        $request->session()->put('_sync_online', $result['status'] !== 'offline');

        return response()->json($result);
    }
}