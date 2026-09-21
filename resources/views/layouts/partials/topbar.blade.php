{{-- Barra superior: notificaciones + estado de conexión (como en una web normal) --}}
<div class="sipce-topbar" id="sipceTopbar">
    <div class="topbar-left">
        <span class="topbar-greeting">Hola, <b>{{ Auth::user()?->name ?? 'Usuario' }}</b></span>
    </div>

    <div class="topbar-right">
        @if(Route::has('notificaciones.index'))
        <a href="{{ route('notificaciones.index') }}"
           class="topbar-notif {{ request()->routeIs('notificaciones.*') ? 'active' : '' }}"
           title="Notificaciones" aria-label="Notificaciones">
            <i class="fas fa-bell"></i>
            @php $noLeidas = \App\Models\Notificacion::where('user_id', auth()->id())->where('leida', false)->count(); @endphp
            @if($noLeidas > 0)
                <span class="notif-badge">{{ $noLeidas > 99 ? '99+' : $noLeidas }}</span>
            @endif
        </a>
        @endif

        <button type="button" class="topbar-sync" id="syncStatus" title="Sincronización con el servidor (clic para sincronizar ahora)">
            <i class="fas fa-sync" id="syncIcon"></i>
            <span id="syncLabel">Sincronizar</span>
        </button>

        <div class="topbar-wifi" id="wifiStatus" title="Verificando conexión...">
            <i class="fas fa-wifi" id="wifiIcon"></i>
            <span id="wifiLabel">Verificando...</span>
        </div>
    </div>
</div>

<style>
    .sipce-topbar {
        position: sticky;
        top: 12px;
        z-index: 940;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
        padding: 10px 16px;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(10px);
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 4px 16px rgba(2, 6, 23, 0.06);
    }

    .topbar-left {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-right: auto;
        min-width: 0;
    }

    .topbar-greeting {
        font-size: 0.95rem;
        font-weight: 600;
        color: #334155;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .topbar-greeting b {
        color: var(--sipce-primary, #667eea);
    }

    .topbar-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }

    .topbar-notif {
        position: relative;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 1.05rem;
        text-decoration: none;
        transition: all 0.2s;
    }

    .topbar-notif:hover {
        background: #eef2ff;
        border-color: rgba(var(--sipce-primary-rgb), 0.5);
        color: var(--sipce-primary);
    }

    .topbar-notif.active {
        color: var(--sipce-primary);
        border-color: var(--sipce-primary);
        background: #eef2ff;
    }

    .topbar-notif .notif-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        min-width: 18px;
        height: 18px;
        line-height: 16px;
        padding: 0 5px;
        background: #ef4444;
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        border-radius: 9px;
        text-align: center;
        border: 2px solid #fff;
        pointer-events: none;
    }

    .topbar-wifi {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        cursor: default;
        transition: all 0.3s ease;
    }

    .topbar-sync {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .topbar-sync:hover {
        background: #eef2ff;
        border-color: rgba(var(--sipce-primary-rgb), 0.5);
        color: var(--sipce-primary);
    }

    .topbar-sync.syncing {
        color: var(--sipce-primary);
        border-color: var(--sipce-primary);
    }

    .topbar-sync.syncing i {
        animation: sipce-spin 1s linear infinite;
    }

    .topbar-sync.pending {
        color: #b45309;
        background: #fffbeb;
        border-color: #fde68a;
    }

    .topbar-sync.synced {
        color: #15803d;
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    @keyframes sipce-spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .topbar-wifi i {
        font-size: 1rem;
    }

    .topbar-wifi.online {
        color: #15803d;
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .topbar-wifi.online i {
        color: #22c55e;
        text-shadow: 0 0 8px rgba(34, 197, 94, 0.4);
    }

    .topbar-wifi.offline {
        color: #b91c1c;
        background: #fef2f2;
        border-color: #fecaca;
    }

    .topbar-wifi.offline i {
        color: #ef4444;
        text-shadow: 0 0 8px rgba(239, 68, 68, 0.4);
    }

    .topbar-wifi.disconnected {
        color: #64748b;
    }

    @media (max-width: 768px) {
        .sipce-topbar {
            top: 8px;
            margin-bottom: 14px;
            padding: 8px 12px;
        }

        .topbar-greeting {
            display: none;
        }
    }
</style>

<script>
    (function () {
        const icon = document.getElementById('wifiIcon');
        const label = document.getElementById('wifiLabel');
        const status = document.getElementById('wifiStatus');
        if (!icon || !label || !status) return;

        const ENDPOINT = '{{ url("up") }}';
        const TIMEOUT_MS = 4000;

        function setOnline() {
            status.className = 'topbar-wifi online';
            icon.className = 'fas fa-wifi';
            label.textContent = 'Conectado';
            status.title = 'Conexión estable';
            if (window.sipceSync) refreshSyncStatus();
        }

        function setOffline() {
            status.className = 'topbar-wifi offline';
            icon.className = 'fas fa-wifi';
            label.textContent = 'Sin internet';
            status.title = 'Conectado pero sin acceso a internet';
        }

        function setDisconnected() {
            status.className = 'topbar-wifi disconnected';
            icon.className = 'fas fa-wifi';
            label.textContent = 'Sin conexión';
            status.title = 'No hay conexión de red';
        }

        async function checkConnection() {
            if (!navigator.onLine) {
                setDisconnected();
                return;
            }
            try {
                const controller = new AbortController();
                const timeout = setTimeout(() => controller.abort(), TIMEOUT_MS);
                const resp = await fetch(ENDPOINT, {
                    method: 'HEAD',
                    signal: controller.signal,
                    cache: 'no-store'
                });
                clearTimeout(timeout);
                resp.ok ? setOnline() : setOffline();
            } catch (e) {
                setOffline();
            }
        }

        checkConnection();
        window.addEventListener('online', () => checkConnection());
        window.addEventListener('offline', () => setDisconnected());
        setInterval(checkConnection, 15000);

        // =============================================
        // SINCRONIZACIÓN (estado + disparo manual)
        // =============================================
        const syncEl = document.getElementById('syncStatus');
        const syncLabel = document.getElementById('syncLabel');

        async function refreshSyncStatus() {
            if (!syncEl || !syncLabel) return;
            try {
                const resp = await fetch('{{ route('sync.status') }}', { cache: 'no-store' });
                const data = await resp.json();
                const pendientes = data.pending || 0;
                if (data.syncing) {
                    syncEl.className = 'topbar-sync syncing';
                    syncLabel.textContent = 'Sincronizando...';
                } else if (pendientes > 0) {
                    syncEl.className = 'topbar-sync pending';
                    syncLabel.textContent = pendientes + ' pendiente' + (pendientes === 1 ? '' : 's');
                } else {
                    syncEl.className = typeof data.last_sync === 'string'
                        ? 'topbar-sync synced'
                        : 'topbar-sync';
                    syncLabel.textContent = typeof data.last_sync === 'string'
                        ? 'Sincronizado'
                        : 'Sincronizar';
                }
            } catch (e) {
                // sin conexión: el chip wifi ya lo indica
            }
        }

        syncEl.addEventListener('click', async () => {
            syncEl.className = 'topbar-sync syncing';
            syncLabel.textContent = 'Sincronizando...';
            try {
                await fetch('{{ route('sync.run') }}', { cache: 'no-store' });
            } catch (e) { }
            refreshSyncStatus();
        });

        window.sipceSync = true;
        refreshSyncStatus();
        setInterval(refreshSyncStatus, 45000);
    })();
</script>