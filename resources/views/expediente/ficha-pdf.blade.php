<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SIPCE - Ficha Clínica del Paciente</title>
    @include('reportes.pdf.partials.estilos')
    <style>
        body { font-size: 10px; line-height: 1.45; }
        .section { margin-bottom: 16px; page-break-inside: avoid; }
        .section-title { font-size: 12px; font-weight: bold; color: #1F3864; border-bottom: 2px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .info-grid td { padding: 4px 7px; border: 0.5px solid #cbd5e1; vertical-align: top; }
        .info-grid .label { font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: 600; background: #f1f5f9; width: 30%; }
        .info-grid .valor { font-size: 10px; color: #1e293b; font-weight: 500; }
        .bloque { padding: 6px 8px; margin-bottom: 6px; background: #f8fafc; border-left: 3px solid #1F3864; }
        .bloque p { margin: 2px 0; }
        .bloque .fecha { font-size: 8px; color: #64748b; font-style: italic; }
        .fila-compacta { margin-bottom: 4px; }
        .fila-compacta .campo { font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: 600; }
        .fila-compacta .dato { font-size: 10px; }
        .sub-seccion { font-size: 10px; font-weight: bold; color: #334155; margin: 8px 0 4px 0; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    @include('reportes.pdf.partials.encabezado')

    <div class="titulo">FICHA CLÍNICA DEL PACIENTE</div>
    <p class="meta">Generado: {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp; Expediente Nº {{ $paciente->numero_expediente }}</p>

    {{-- 1. IDENTIFICACIÓN --}}
    <div class="section">
        <div class="section-title">Identificación del Paciente</div>
        <table class="info-grid">
            <tr>
                <td class="label">Nombre completo</td>
                <td class="valor">{{ $paciente->detalle->nombre ?? '' }} {{ $paciente->detalle->apellido ?? '' }}</td>
                <td class="label">Cédula</td>
                <td class="valor">{{ $paciente->cedula_paciente ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Fecha de nacimiento</td>
                <td class="valor">{{ $paciente->detalle->fecha_nacimiento ? $paciente->detalle->fecha_nacimiento->format('d/m/Y') : 'N/A' }}</td>
                <td class="label">Edad</td>
                <td class="valor">{{ $paciente->detalle->fecha_nacimiento ? \Carbon\Carbon::parse($paciente->detalle->fecha_nacimiento)->age . ' años' : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Tipo de paciente</td>
                <td class="valor">{{ ucfirst($paciente->tipo_paciente ?? 'N/A') }}</td>
                <td class="label">Sexo / Género</td>
                <td class="valor">{{ $paciente->detalle->genero ?? $paciente->detalle->sexo ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Teléfono</td>
                <td class="valor">{{ $paciente->telefono ?? 'N/A' }}</td>
                <td class="label">Correo electrónico</td>
                <td class="valor">{{ $paciente->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Municipio</td>
                <td class="valor">{{ $paciente->municipio ?? 'N/A' }}</td>
                <td class="label">Tipo de atención</td>
                <td class="valor">{{ ucfirst($paciente->tipo_atencion ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="label">Prioridad</td>
                <td class="valor">{{ ucfirst($paciente->prioridad ?? 'N/A') }}</td>
                <td class="label">Estado</td>
                <td class="valor">{{ $paciente->estado?->tipo ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Acceso al sistema</td>
                <td class="valor">{{ $paciente->user ? 'Sí (' . $paciente->user->name . ')' : 'No' }}</td>
                <td class="label">Registro creado</td>
                <td class="valor">{{ $paciente->created_at ? $paciente->created_at->format('d/m/Y') : 'N/A' }}</td>
            </tr>
        </table>
    </div>

    {{-- 2. DATOS SEGÚN TIPO DE PACIENTE --}}
    <div class="section">
        <div class="section-title">Datos Específicos</div>

        @php
            $data = $paciente->detalle ? $paciente->detalle->getAttributes() : [];
            $excluir = ['uuid', 'id', 'nombre', 'apellido', 'cedula', 'fecha_nacimiento', 'genero', 'sexo',
                        'telefono', 'telefono_personal', 'telefono_representante', 'telefono_contacto',
                        'email', 'email_personal', 'email_representante', 'direccion', 'created_at', 'updated_at',
                        'fecha_entrevista', 'pagina', 'caratula', 'admin_tematicas', 'seccion_resumen', 'section1',
                        'section2', 'section3'];
        @endphp

        @foreach ($data as $campo => $valor)
            @if (in_array($campo, $excluir) || is_array($valor) || is_object($valor) || $valor === null || $valor === '')
                @continue
            @endif
            <div class="fila-compacta">
                <span class="campo">{{ ucwords(str_replace('_', ' ', $campo)) }}:</span>
                <span class="dato">{{ $valor }}</span>
            </div>
        @endforeach
    </div>

    {{-- 3. MOTIVO Y DIAGNÓSTICO INICIAL --}}
    <div class="section">
        <div class="section-title">Motivo de Consulta y Valoración Inicial</div>
        <div class="bloque">
            <p><strong>Motivo de consulta:</strong></p>
            <p>{{ $paciente->motivo_consulta ?? 'No registrado' }}</p>
            @if($paciente->diagnostico_preliminar)
            <p style="margin-top:6px;"><strong>Diagnóstico preliminar:</strong> {{ $paciente->diagnostico_preliminar }}</p>
            @endif
        </div>
    </div>

    {{-- 4. DIAGNÓSTICOS --}}
    <div class="section">
        <div class="section-title">Diagnósticos Registrados</div>
        @if($paciente->diagnosticos->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">CIE-10</th>
                    <th>Diagnóstico</th>
                    <th style="width: 80px;">Principal</th>
                    <th style="width: 70px;">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paciente->diagnosticos as $diag)
                <tr>
                    <td>{{ $diag->codigo_cie ?? '—' }}</td>
                    <td>
                        {{ $diag->diagnostico }}
                        @if($diag->observaciones)
                        <br><span style="color:#94a3b8; font-size:8px;">{{ $diag->observaciones }}</span>
                        @endif
                    </td>
                    <td class="center">{{ $diag->es_principal ? 'Sí' : 'No' }}</td>
                    <td class="center">{{ $diag->fecha ? $diag->fecha->format('d/m/Y') : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="color:#94a3b8;">Sin diagnósticos registrados.</p>
        @endif
    </div>

    {{-- 5. HISTORIAL DE CITAS --}}
    <div class="section">
        <div class="section-title">Historial de Citas</div>
        @if($paciente->citas->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 70px;">Fecha</th>
                    <th style="width: 85px;">Estado</th>
                    <th>Objetivo</th>
                    <th style="width: 130px;">Planificación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paciente->citas as $cita)
                <tr>
                    <td class="center">{{ $cita->fecha ? $cita->fecha->format('d/m/Y') : '—' }}</td>
                    <td class="center">{{ $cita->estado_texto }}</td>
                    <td>{{ mb_substr($cita->objetivo ?? '', 0, 140) }}</td>
                    <td>{{ mb_substr($cita->planificacion ?? '', 0, 140) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="color:#94a3b8;">Sin citas registradas.</p>
        @endif
    </div>

    {{-- 6. SESIONES CLÍNICAS --}}
    <div class="section">
        <div class="section-title">Sesiones Clínicas ({{ $paciente->sesiones->count() }})</div>
        @forelse($paciente->sesiones as $sesion)
        <div class="bloque">
            <div class="fecha">
                {{ $sesion->fecha ? $sesion->fecha->format('d/m/Y') : 'N/A' }}
                &nbsp;|&nbsp; Duración: {{ $sesion->duracion_formato }}
                &nbsp;|&nbsp; Terapeuta: {{ $sesion->user->name ?? 'N/A' }}
            </div>
            @if($sesion->resumen)
            <p><strong>Resumen:</strong> {{ $sesion->resumen }}</p>
            @endif
            @if($sesion->observaciones_clinicas)
            <p><em>Observaciones clínicas:</em> {{ $sesion->observaciones_clinicas }}</p>
            @endif
        </div>
        @empty
        <p style="color:#94a3b8;">Sin sesiones registradas.</p>
        @endforelse
    </div>

    {{-- 7. PLANES DE TRATAMIENTO --}}
    <div class="section">
        <div class="section-title">Planes de Tratamiento</div>
        @forelse($paciente->planesTratamiento as $plan)
        <div class="bloque">
            <p>
                <strong>{{ $plan->titulo }}</strong>
                &nbsp;<span style="color:#64748b;">[{{ $plan->estado_texto }}]</span>
                &nbsp;<span class="fecha">
                    {{ $plan->fecha_inicio ? $plan->fecha_inicio->format('d/m/Y') : '—' }}
                    @if($plan->fecha_fin_estimada) a {{ $plan->fecha_fin_estimada->format('d/m/Y') }}@endif
                </span>
            </p>
            @if($plan->objetivo_general)
            <p><em>Objetivo general:</em> {{ $plan->objetivo_general }}</p>
            @endif
            @if($plan->objetivos->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 30%;">Objetivo</th>
                        <th style="width: 45%;">Descripción</th>
                        <th style="width: 25%;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plan->objetivos as $objetivo)
                    <tr>
                        <td>{{ $objetivo->descripcion }}</td>
                        <td>{{ $objetivo->meta }}</td>
                        <td class="center">{{ $objetivo->estado_texto }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
        @empty
        <p style="color:#94a3b8;">Sin planes de tratamiento.</p>
        @endforelse
    </div>

    {{-- 8. NOTAS CLÍNICAS --}}
    <div class="section">
        <div class="section-title">Notas y Anotaciones ({{ $paciente->notas->count() }})</div>
        @forelse($paciente->notas as $nota)
        <div class="bloque">
            <div class="fecha">{{ $nota->user->name ?? 'Usuario' }} &mdash; {{ $nota->created_at->format('d/m/Y H:i') }}</div>
            <p>{{ $nota->anotacion }}</p>
        </div>
        @empty
        <p style="color:#94a3b8;">Sin notas registradas.</p>
        @endforelse
    </div>

    {{-- 9. ACOMPAÑANTES --}}
    <div class="section">
        <div class="section-title">Acompañantes</div>
        @if($paciente->acompanantes->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th style="width: 100px;">Parentesco</th>
                    <th style="width: 110px;">Cédula</th>
                    <th style="width: 110px;">Teléfono</th>
                    <th style="width: 70px;">Principal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paciente->acompanantes as $acompanante)
                <tr>
                    <td>{{ $acompanante->nombre }}</td>
                    <td>{{ $acompanante->parentesco }}</td>
                    <td>{{ $acompanante->cedula ?? '—' }}</td>
                    <td>{{ $acompanante->telefono ?? '—' }}</td>
                    <td class="center">{{ $acompanante->es_principal ? 'Sí' : 'No' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="color:#94a3b8;">Sin acompañantes registrados.</p>
        @endif
    </div>

    {{-- 10. DIARIO DEL PACIENTE --}}
    @if($paciente->user && $diarios->count() > 0)
    <div class="section">
        <div class="section-title">Diario del Paciente ({{ $diarios->count() }})</div>
        @foreach($diarios as $diario)
        <div class="bloque">
            <div class="fecha">{{ $diario->fecha ? \Carbon\Carbon::parse($diario->fecha)->format('d/m/Y') : $diario->created_at->format('d/m/Y H:i') }}</div>
            <p>{{ mb_substr($diario->contenido, 0, 300) }}</p>
        </div>
        @endforeach
    </div>
    @endif

    @include('reportes.pdf.partials.firmas')

    <div class="footer" style="text-align:center; border-top:1px solid #cbd5e1; margin-top:12px; padding-top:4px; font-size:7px; color:#94a3b8;">
        SIPCE &mdash; Sistema de Información de Pacientes de Salud Mental &nbsp;|&nbsp; Documento generado automáticamente el {{ now()->format('d/m/Y H:i') }}
    </div>

    @include('reportes.pdf.partials.pie')
</body>
</html>