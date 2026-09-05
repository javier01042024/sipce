<div id="tab-notas" class="tab-panel">

    {{-- CSS TEMPORAL PARA NOTAS --}}
    <style>
        /* ==========================================
           NOTAS - ESTILOS COMPLETOS
           Paleta: Azul marino + Morado/Azul
           ========================================== */

        .notas-card {
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(var(--sipce-primary-rgb), 0.12);
            border: 1px solid rgba(var(--sipce-primary-rgb), 0.08);
            margin-bottom: 25px;
        }

        .notas-header {
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
            color: white;
            padding: 18px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .notas-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notas-header h3 i {
            color: #a8edea;
        }

        .btn-nueva-nota {
            background: white;
            color: var(--sipce-primary);
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-nueva-nota:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .notas-body {
            padding: 20px 25px;
        }

        /* Lista de notas */
        .notas-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .nota-item {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%);
            padding: 18px;
            border-radius: 12px;
            border-left: 4px solid var(--sipce-primary);
            transition: all 0.3s;
        }

        .nota-item:hover {
            box-shadow: 0 4px 12px rgba(var(--sipce-primary-rgb), 0.1);
        }

        .nota-cabecera {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .nota-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nota-avatar-small {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--sipce-primary) 0%, var(--sipce-primary-dark) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 15px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .nota-user-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .nota-user-name {
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
        }

        .nota-user-rol {
            font-size: 11px;
            color: var(--sipce-primary);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .nota-fecha {
            font-size: 12px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .nota-hora {
            margin-left: 8px;
            font-size: 11px;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        .nota-contenido {
            color: #334155;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        /* Vínculo con diario */
        .nota-vinculo-diario {
            margin-top: 12px;
            padding: 10px 14px;
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            border-radius: 8px;
            font-size: 12px;
            color: #0369a1;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            border-left: 3px solid #0284c7;
        }

        .nota-vinculo-diario i {
            color: #0284c7;
        }

        .btn-ver-diario {
            margin-left: auto;
            padding: 4px 10px;
            background: white;
            color: #0369a1;
            border-radius: 6px;
            text-decoration: none;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
            border: 1px solid #bae6fd;
        }

        .btn-ver-diario:hover {
            background: #0369a1;
            color: white;
        }

        /* Footer de nota */
        .nota-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid rgba(var(--sipce-primary-rgb), 0.1);
        }

        .btn-eliminar-nota {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
            padding: 5px 10px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-eliminar-nota:hover {
            color: #ef4444;
            background: #fee2e2;
        }

        /* Formulario */
        .form-tab-card {
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(var(--sipce-primary-rgb), 0.12);
            border: 1px solid rgba(var(--sipce-primary-rgb), 0.08);
            margin-top: 20px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-tab-header {
            background: linear-gradient(135deg, var(--sipce-primary) 0%, var(--sipce-primary-dark) 100%);
            color: white;
            padding: 18px 25px;
        }

        .form-tab-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-tab-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #475569;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .form-group label i {
            color: var(--sipce-primary);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
            transition: all 0.3s;
            background: #f8fafc;
        }

        .form-control:focus {
            border-color: var(--sipce-primary);
            box-shadow: 0 0 0 3px rgba(var(--sipce-primary-rgb), 0.1);
            outline: none;
            background: white;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23667eea' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
            padding-right: 40px;
            cursor: pointer;
        }

        .form-hint {
            display: block;
            margin-top: 6px;
            font-size: 11px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .badge-opcional {
            background: #e2e8f0;
            color: #64748b;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
        }

        /* Avisos */
        .sin-diario-aviso,
        .sin-acceso-aviso {
            padding: 12px 15px;
            border-radius: 10px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .sin-diario-aviso {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .sin-diario-aviso i {
            color: #f59e0b;
            font-size: 18px;
        }

        .sin-acceso-aviso {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .sin-acceso-aviso i {
            color: #ef4444;
            font-size: 18px;
        }

        /* Botones formulario */
        .form-actions-tab {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }

        .btn-cancel-tab {
            background: #f1f5f9;
            color: #64748b;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cancel-tab:hover {
            background: #e2e8f0;
        }

        .btn-save-tab {
            background: linear-gradient(135deg, var(--sipce-primary) 0%, var(--sipce-primary-dark) 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(var(--sipce-primary-rgb), 0.3);
        }

        .btn-save-tab:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(var(--sipce-primary-rgb), 0.4);
        }

        /* Estado vacío */
        .empty-state-tab {
            text-align: center;
            padding: 50px 20px;
            color: #94a3b8;
        }

        .empty-icon-tab {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .empty-state-tab h4 {
            font-size: 18px;
            color: #64748b;
            margin-bottom: 10px;
        }

        .empty-state-tab p {
            font-size: 14px;
            color: #94a3b8;
            margin-bottom: 20px;
        }

        .btn-empty-tab {
            background: linear-gradient(135deg, var(--sipce-primary) 0%, var(--sipce-primary-dark) 100%);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(var(--sipce-primary-rgb), 0.3);
        }

        .btn-empty-tab:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(var(--sipce-primary-rgb), 0.4);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .notas-header {
                flex-direction: column;
                text-align: center;
            }

            .btn-nueva-nota {
                width: 100%;
                justify-content: center;
            }

            .nota-cabecera {
                flex-direction: column;
                align-items: flex-start;
            }

            .nota-fecha {
                width: 100%;
            }

            .nota-vinculo-diario {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn-ver-diario {
                margin-left: 0;
            }

            .nota-footer {
                flex-direction: column;
                gap: 10px;
            }

            .form-actions-tab {
                flex-direction: column;
            }

            .btn-cancel-tab,
            .btn-save-tab {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    {{-- LISTADO DE NOTAS --}}
    <div class="notas-card">
        <div class="notas-header">
            <h3>
                <i class="fas fa-sticky-note"></i>
                Notas y Anotaciones
            </h3>
            <button class="btn-nueva-nota" onclick="toggleFormNota()">
                <i class="fas fa-plus-circle"></i>
                Nueva Nota
            </button>
        </div>

        <div class="notas-body">
            @php
            $notas = $paciente->notas ?? collect([]);
            @endphp

            @if($notas->count() > 0)
            <div class="notas-list">
                @foreach($notas as $nota)
                <div class="nota-item">
                    {{-- CABECERA DE LA NOTA --}}
                    <div class="nota-cabecera">
                        <div class="nota-user">
                            <div class="nota-avatar-small">
                                {{ strtoupper(substr($nota->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="nota-user-info">
                                <span class="nota-user-name">
                                    {{ $nota->user->name ?? 'Usuario desconocido' }}
                                </span>
                            </div>
                        </div>
                        <span class="nota-fecha">
                            <i class="far fa-calendar-alt"></i>
                            {{ $nota->created_at->format('d/m/Y') }}
                            <span class="nota-hora">
                                <i class="far fa-clock"></i>
                                {{ $nota->created_at->format('H:i') }}
                            </span>
                        </span>
                    </div>

                    {{-- CONTENIDO --}}
                    <div class="nota-contenido">
                        {{ $nota->anotacion }}
                    </div>

                    {{-- VÍNCULO CON DIARIO --}}
                    @if($nota->diario_id && $nota->diario)
                    <div class="nota-vinculo-diario">
                        <i class="fas fa-link"></i>
                        Vinculado al diario del {{ \Carbon\Carbon::parse($nota->diario->fecha)->format('d/m/Y') }}
                        <a href="{{ route('diarios.show', $nota->diario_id) }}"
                            class="btn-ver-diario"
                            target="_blank">
                            <i class="fas fa-external-link-alt"></i>
                            Ver entrada
                        </a>
                    </div>
                    @endif

                    {{-- FOOTER CON BOTÓN ELIMINAR --}}
                    <div class="nota-footer">
                        <span style="font-size: 11px; color: #94a3b8;">
                            <i class="fas fa-hashtag"></i>
                            ID: {{ $nota->id }}
                        </span>
                        <form action="{{ route('notas.destroy', $nota) }}"
                            method="POST"
                            onsubmit="return confirmarEliminarNota(this);"
                            style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-eliminar-nota">
                                <i class="fas fa-trash-alt"></i>
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state-tab">
                <div class="empty-icon-tab">
                    <i class="fas fa-clipboard"></i>
                </div>
                <h4>No hay notas registradas</h4>
                <p>Las notas y anotaciones sobre el paciente aparecerán aquí</p>
                <button class="btn-empty-tab" onclick="toggleFormNota()">
                    <i class="fas fa-plus-circle"></i>
                    Crear primera nota
                </button>
            </div>
            @endif
        </div>
    </div>

    {{-- FORMULARIO PARA NUEVA NOTA --}}
    <div id="formNotaContainer" class="form-tab-card" style="display: none;">
        <div class="form-tab-header">
            <h3>
                <i class="fas fa-pen"></i>
                Nueva Anotación para {{ $paciente->nombre_completo }}
            </h3>
        </div>

        <div class="form-tab-body">
            <form action="{{ route('notas.store') }}" method="POST">
                @csrf

                <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">

                <div class="form-group">
                    <label for="anotacion">
                        <i class="fas fa-pencil-alt"></i>
                        Anotación
                    </label>
                    <textarea
                        name="anotacion"
                        id="anotacion"
                        rows="5"
                        class="form-control"
                        placeholder="Escriba la anotación, observación o nota sobre el paciente..."
                        required>{{ old('anotacion') }}</textarea>
                    <small class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        Esta nota quedará registrada en el expediente del paciente
                    </small>
                </div>

                {{-- VINCULAR CON DIARIO (SOLO SI EL PACIENTE TIENE USUARIO) --}}
                @if($paciente->user)
                @php
                $diariosPaciente = \App\Models\Diario::where('user_id', $paciente->user_id)
                ->orderBy('fecha', 'desc')
                ->limit(20)
                ->get();
                @endphp

                @if($diariosPaciente->count() > 0)
                <div class="form-group">
                    <label for="diario_id">
                        <i class="fas fa-link"></i>
                        Vincular con entrada del diario
                        <span class="badge-opcional">Opcional</span>
                    </label>
                    <select name="diario_id" id="diario_id" class="form-control">
                        <option value="">Sin vincular</option>
                        @foreach($diariosPaciente as $diario)
                        <option value="{{ $diario->id }}"
                            {{ old('diario_id') == $diario->id ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($diario->fecha)->format('d/m/Y') }} -
                            {{ Str::limit($diario->contenido, 80) }}
                        </option>
                        @endforeach
                    </select>
                    <small class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        Puede vincular esta nota con una entrada del diario del paciente
                    </small>
                </div>
                @else
                <div class="sin-diario-aviso">
                    <i class="fas fa-info-circle"></i>
                    <span>El paciente aún no tiene entradas en su diario. La nota se guardará sin vincular.</span>
                </div>
                @endif
                @else
                <div class="sin-acceso-aviso">
                    <i class="fas fa-user-lock"></i>
                    <span>Este paciente no tiene acceso al sistema, por lo tanto no tiene diario. La nota se guardará sin vincular.</span>
                </div>
                @endif

                <div class="form-actions-tab">
                    <button type="button" class="btn-cancel-tab" onclick="toggleFormNota()">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn-save-tab">
                        <i class="fas fa-save"></i>
                        Guardar Nota
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- SCRIPT PARA TOGGLE DEL FORMULARIO --}}
<script>
    function toggleFormNota() {
        var form = document.getElementById('formNotaContainer');
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
            // Scroll suave hasta el formulario
            form.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            // Focus en el textarea
            setTimeout(() => {
                document.getElementById('anotacion').focus();
            }, 300);
        } else {
            form.style.display = 'none';
        }
    }

    function confirmarEliminarNota(form) {
        SIPCE_ALERT.confirmDelete({
            title: '¿Eliminar nota?',
            text: 'Esta acción no se puede deshacer.'
        }).then(function(result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
        return false;
    }
</script>