<div id="tab-sesiones" class="tab-panel">

    <style>
        .sesiones-card {
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.12);
            border: 1px solid rgba(102, 126, 234, 0.08);
            margin-bottom: 25px;
        }
        .sesiones-header {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
            padding: 18px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .sesiones-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sesiones-header h3 i { color: #bae6fd; }
        .sesiones-body { padding: 20px 25px; }
        .sesion-item {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            padding: 18px;
            border-radius: 12px;
            border-left: 4px solid #0ea5e9;
            margin-bottom: 12px;
            transition: all 0.3s;
        }
        .sesion-item:hover { box-shadow: 0 4px 12px rgba(14, 165, 233, 0.12); }
        .sesion-item:last-child { margin-bottom: 0; }
        .sesion-cabecera {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .sesion-meta {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }
        .sesion-fecha {
            font-size: 13px;
            color: #0369a1;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .sesion-duracion {
            font-size: 12px;
            color: #64748b;
            background: white;
            padding: 3px 10px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border: 1px solid #bae6fd;
        }
        .sesion-terapeuta {
            font-size: 12px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .sesion-resumen {
            color: #334155;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .sesion-observaciones {
            margin-top: 10px;
            padding: 10px 14px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            font-size: 13px;
            color: #475569;
            border-left: 3px solid #0ea5e9;
        }
        .sesion-observaciones strong {
            display: block;
            margin-bottom: 4px;
            color: #0369a1;
            font-size: 12px;
        }
        .sesion-footer {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid rgba(14, 165, 233, 0.15);
        }
        .btn-sesion-ver {
            padding: 6px 14px;
            background: white;
            color: #0369a1;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
        }
        .btn-sesion-ver:hover { background: #0ea5e9; color: white; border-color: #0ea5e9; }
        .btn-sesion-edit {
            padding: 6px 14px;
            background: white;
            color: #667eea;
            border: 1px solid #c7d2fe;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
        }
        .btn-sesion-edit:hover { background: #667eea; color: white; border-color: #667eea; }
        @media (max-width: 768px) {
            .sesion-cabecera { flex-direction: column; align-items: flex-start; }
            .sesion-meta { width: 100%; }
        }
    </style>

    <div class="sesiones-card">
        <div class="sesiones-header">
            <h3>
                <i class="fas fa-head-side-virus"></i>
                Sesiones Clínicas
            </h3>
            <a href="{{ route('sesiones.create') }}" class="btn-nueva-nota" style="background:white;color:#0ea5e9;">
                <i class="fas fa-plus-circle"></i>
                Nueva Sesión
            </a>
        </div>

        <div class="sesiones-body">
            @php
                $sesiones = $paciente->sesiones ?? collect([]);
            @endphp

            @if($sesiones->count() > 0)
                @foreach($sesiones->sortByDesc('fecha') as $sesion)
                <div class="sesion-item">
                    <div class="sesion-cabecera">
                        <div class="sesion-meta">
                            <span class="sesion-fecha">
                                <i class="far fa-calendar-alt"></i>
                                {{ $sesion->fecha ? $sesion->fecha->format('d/m/Y') : 'N/A' }}
                            </span>
                            <span class="sesion-duracion">
                                <i class="far fa-clock"></i>
                                {{ $sesion->duracion_formato }}
                            </span>
                            <span class="sesion-terapeuta">
                                <i class="fas fa-user-md"></i>
                                {{ $sesion->user->name ?? 'N/A' }}
                            </span>
                        </div>
                    </div>

                    @if($sesion->resumen)
                    <div class="sesion-resumen">
                        {{ $sesion->resumen }}
                    </div>
                    @endif

                    @if($sesion->observaciones_clinicas)
                    <div class="sesion-observaciones">
                        <strong><i class="fas fa-notes-medical"></i> Observaciones clínicas</strong>
                        {{ $sesion->observaciones_clinicas }}
                    </div>
                    @endif

                    <div class="sesion-footer">
                        <a href="{{ route('sesiones.show', $sesion) }}" class="btn-sesion-ver">
                            <i class="fas fa-eye"></i> Ver detalle
                        </a>
                        <a href="{{ route('sesiones.edit', $sesion) }}" class="btn-sesion-edit">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>
                @endforeach
            @else
                <div class="empty-state-tab">
                    <div class="empty-icon-tab">
                        <i class="fas fa-clipboard"></i>
                    </div>
                    <h4>No hay sesiones registradas</h4>
                    <p>Las sesiones clínicas de este paciente aparecerán aquí</p>
                    <a href="{{ route('sesiones.create') }}" class="btn-empty-tab">
                        <i class="fas fa-plus-circle"></i>
                        Registrar primera sesión
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>
