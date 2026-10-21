{{-- Barra superior: notificaciones + estado de conexión (como en una web normal) --}}
<div class="sipce-topbar" id="sipceTopbar">
    <div class="topbar-left">
        <span class="topbar-greeting">Hola, <b>{{ Auth::user()?->name ?? 'Usuario' }}</b></span>
    </div>

    <div class="topbar-right">
        @if(Route::has('notificaciones.index'))
        <div class="topbar-notif-wrap">
            @php
                $noLeidas = \App\Models\Notificacion::where('user_id', auth()->id())
                    ->where('leida', false)
                    ->where('fecha_hora', '>=', now()->subDays(\App\Models\Notificacion::DIAS_RETENCION))
                    ->count();
            @endphp
            <button type="button" id="notifBell"
                   class="topbar-notif {{ request()->routeIs('notificaciones.*') ? 'active' : '' }}"
                   title="Notificaciones" aria-label="Notificaciones"
                   aria-haspopup="dialog" aria-expanded="false" aria-controls="notifPanel">
                <i class="fas fa-bell"></i>
                <span class="notif-badge" id="notifBadge" @if($noLeidas == 0) style="display:none;" @endif>{{ $noLeidas > 99 ? '99+' : $noLeidas }}</span>
            </button>

            <div class="notif-panel" id="notifPanel" role="dialog" aria-label="Notificaciones" hidden>
                <div class="notif-panel-head">
                    <span class="notif-panel-title"><i class="fas fa-bell"></i> Notificaciones</span>
                    <button type="button" class="notif-panel-all" id="notifMarcarTodas">Marcar todas como leídas</button>
                </div>
                <div class="notif-panel-list" id="notifLista">
                    <div class="notif-panel-loading"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>
                </div>
                <div class="notif-panel-foot">
                    <span><i class="far fa-clock"></i> Se borran solas a los {{ \App\Models\Notificacion::DIAS_RETENCION }} días</span>
                </div>
            </div>
        </div>
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

    .topbar-notif-wrap {
        position: relative;
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
        padding: 0;
        font-family: inherit;
        cursor: pointer;
    }

    .topbar-notif:hover,
    .topbar-notif[aria-expanded="true"] {
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

    /* =============================================
       PANEL DE NOTIFICACIONES (estilo YouTube)
       ============================================= */
    @keyframes notifIn {
        from { opacity: 0; transform: translateY(-8px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .notif-panel {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: min(380px, calc(100vw - 32px));
        max-height: 480px;
        display: flex;
        flex-direction: column;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(2, 6, 23, 0.18);
        z-index: 960;
        overflow: hidden;
        animation: notifIn 0.18s ease-out;
    }

    .notif-panel[hidden] {
        display: none;
    }

    .notif-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        flex-shrink: 0;
    }

    .notif-panel-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .notif-panel-title i {
        color: var(--sipce-primary);
    }

    .notif-panel-all {
        border: 0;
        background: none;
        color: var(--sipce-primary);
        font-size: 0.75rem;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        padding: 4px 6px;
        border-radius: 8px;
        transition: background 0.2s;
        white-space: nowrap;
    }

    .notif-panel-all:hover {
        background: rgba(var(--sipce-primary-rgb), 0.12);
    }

    .notif-panel-list {
        flex: 1;
        overflow-y: auto;
        overscroll-behavior: contain;
    }

    .notif-panel-foot {
        flex-shrink: 0;
        padding: 9px 16px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
        font-size: 0.72rem;
        font-weight: 600;
        color: #94a3b8;
        text-align: center;
    }

    .notif-panel-foot i {
        margin-right: 4px;
    }

    .notif-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        width: 100%;
        padding: 13px 16px;
        border: 0;
        border-bottom: 1px solid #f1f5f9;
        background: #fff;
        text-align: left;
        font-family: inherit;
        cursor: pointer;
        transition: background 0.15s;
    }

    .notif-item:hover {
        background: #f8fafc;
    }

    .notif-item.no-leida {
        background: #f0f4ff;
    }

    .notif-item.no-leida:hover {
        background: #e6ecff;
    }

    .notif-item-icono {
        position: relative;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: var(--notif-color, var(--sipce-primary));
        font-size: 0.9rem;
    }

    .notif-item.no-leida .notif-item-icono {
        background: var(--notif-color, var(--sipce-primary));
        color: #fff;
    }

    .notif-dot {
        position: absolute;
        top: -1px;
        right: -1px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--notif-color, var(--sipce-primary));
        border: 2px solid #fff;
        box-shadow: 0 0 6px rgba(2, 6, 23, 0.15);
    }

    .notif-item-cuerpo {
        display: flex;
        flex-direction: column;
        gap: 3px;
        min-width: 0;
        flex: 1;
    }

    .notif-item-titulo {
        font-size: 0.86rem;
        color: #1e293b;
        font-weight: 400;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .notif-item.no-leida .notif-item-titulo {
        font-weight: 700;
    }

    .notif-item-mensaje {
        font-size: 0.78rem;
        color: #64748b;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .notif-item-fecha {
        font-size: 0.7rem;
        color: #94a3b8;
        font-weight: 600;
    }

    .notif-panel-loading,
    .notif-panel-empty {
        padding: 38px 20px;
        text-align: center;
        color: #94a3b8;
        font-size: 0.85rem;
    }

    .notif-panel-empty i {
        display: block;
        font-size: 30px;
        color: #cbd5e1;
        margin-bottom: 10px;
    }

    .notif-panel-empty p {
        margin: 0 0 4px;
        font-weight: 600;
        color: #64748b;
    }

    .notif-panel-empty span {
        font-size: 0.75rem;
    }

    @media (max-width: 768px) {
        .notif-panel {
            position: fixed;
            top: 64px;
            right: 12px;
            left: 12px;
            width: auto;
        }
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

<script>
    // =============================================
    // PANEL DE NOTIFICACIONES (estilo YouTube)
    // =============================================
    (function () {
        const bell = document.getElementById('notifBell');
        if (!bell) return;

        const panel = document.getElementById('notifPanel');
        const lista = document.getElementById('notifLista');
        const badge = document.getElementById('notifBadge');
        const btnTodas = document.getElementById('notifMarcarTodas');
        const BASE = @json(url('/notificaciones'));
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const ESTILOS = {
            cita_proxima:   { icono: 'fa-calendar-check', color: '#f59e0b' },
            cita_confirmada:{ icono: 'fa-calendar-check', color: '#22c55e' },
            recordatorio:   { icono: 'fa-bell',           color: '#6366f1' },
            session:        { icono: 'fa-notes-medical',  color: '#0ea5e9' },
            sistema:        { icono: 'fa-gear',           color: '#64748b' }
        };
        const PREDETERMINADO = { icono: 'fa-bell', color: '#667eea' };

        function escapar(texto) {
            return String(texto == null ? '' : texto)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function pintarBadge(n) {
            n = Number(n) || 0;
            badge.textContent = n > 99 ? '99+' : String(n);
            badge.style.display = n > 0 ? '' : 'none';
        }

        function quitarPunto(item) {
            item.classList.remove('no-leida');
            const punto = item.querySelector('.notif-dot');
            if (punto) punto.remove();
        }

        function pintar(json) {
            pintarBadge(json.no_leidas);
            const items = json.notificaciones || [];

            if (!items.length) {
                lista.innerHTML = '<div class="notif-panel-empty">' +
                    '<i class="fas fa-bell-slash"></i>' +
                    '<p>No tienes notificaciones</p>' +
                    '<span>Estás al día con todas tus notificaciones</span>' +
                    '</div>';
                return;
            }

            lista.innerHTML = items.map(function (n) {
                const estilo = ESTILOS[n.tipo] || PREDETERMINADO;
                const sinLeer = n.leida ? '' : ' no-leida';
                const punto = n.leida ? '' : '<span class="notif-dot"></span>';

                return '<button type="button" class="notif-item' + sinLeer + '"' +
                    ' data-id="' + escapar(n.id) + '"' +
                    ' data-url="' + escapar(n.url || '') + '"' +
                    ' style="--notif-color:' + estilo.color + '">' +
                    '<span class="notif-item-icono">' +
                        '<i class="fas ' + estilo.icono + '"></i>' + punto +
                    '</span>' +
                    '<span class="notif-item-cuerpo">' +
                        '<span class="notif-item-titulo">' + escapar(n.titulo) + '</span>' +
                        '<span class="notif-item-mensaje">' + escapar(n.mensaje) + '</span>' +
                        '<span class="notif-item-fecha">' + escapar(n.fecha || '') + '</span>' +
                    '</span>' +
                    '</button>';
            }).join('');
        }

        async function cargar() {
            lista.innerHTML = '<div class="notif-panel-loading"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';
            try {
                const resp = await fetch(BASE + '/recientes', {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store'
                });
                if (!resp.ok) throw new Error('HTTP ' + resp.status);
                pintar(await resp.json());
            } catch (e) {
                lista.innerHTML = '<div class="notif-panel-empty">' +
                    '<i class="fas fa-triangle-exclamation"></i>' +
                    '<p>No se pudieron cargar</p>' +
                    '<span>Intenta de nuevo en unos segundos</span>' +
                    '</div>';
            }
        }

        function abrir() {
            panel.removeAttribute('hidden');
            bell.setAttribute('aria-expanded', 'true');
            cargar();
        }

        function cerrar() {
            panel.setAttribute('hidden', '');
            bell.setAttribute('aria-expanded', 'false');
        }

        bell.addEventListener('click', function (e) {
            e.stopPropagation();
            if (panel.hasAttribute('hidden')) abrir();
            else cerrar();
        });

        panel.addEventListener('click', function (e) { e.stopPropagation(); });

        document.addEventListener('click', function () {
            if (!panel.hasAttribute('hidden')) cerrar();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !panel.hasAttribute('hidden')) cerrar();
        });

        lista.addEventListener('click', async function (e) {
            const item = e.target.closest('.notif-item');
            if (!item) return;

            const id = item.getAttribute('data-id');
            const url = item.getAttribute('data-url');

            if (item.classList.contains('no-leida')) {
                quitarPunto(item);
                pintarBadge((parseInt(badge.textContent, 10) || 0) - 1);
                try {
                    await fetch(BASE + '/' + id + '/leer', {
                        method: 'PATCH',
                        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                        cache: 'no-store'
                    });
                } catch (err) { }
            }

            cerrar();
            if (url) window.location.href = url;
        });

        btnTodas.addEventListener('click', async function (e) {
            e.stopPropagation();
            btnTodas.disabled = true;
            try {
                await fetch(BASE + '/leer-todas', {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    cache: 'no-store'
                });
            } catch (err) { }
            lista.querySelectorAll('.notif-item.no-leida').forEach(quitarPunto);
            pintarBadge(0);
            btnTodas.disabled = false;
        });
    })();
</script>