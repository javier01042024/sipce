@extends('layouts.app')

@section('content')
{{-- AsegÃºrate de que estos archivos CSS existan --}}
<link href="{{ asset('css/pacientes.css') }}" rel="stylesheet">
<link href="{{ asset('css/pacientes-table.css') }}" rel="stylesheet">

<div class="pacientes-wrapper" style="max-width:1200px;margin:0 auto;padding:20px;">
    
    <div class="pacientes-header">
        <div>
            <h1><i class="fas fa-users"></i> Pacientes</h1>
            <p>GestiÃ³n de pacientes del sistema</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button class="btn-nuevo" onclick="abrirModal('adulto')" style="background:white;color:var(--sipce-primary);">
                <i class="fas fa-user-tie"></i> Adulto
            </button>
            <button class="btn-nuevo" onclick="abrirModal('adolescente')" style="background:white;color:#f59e0b;">
                <i class="fas fa-user"></i> Adolescente
            </button>
            <button class="btn-nuevo" onclick="abrirModal('niÃ±o')" style="background:white;color:#10b981;">
                <i class="fas fa-child"></i> NiÃ±o
            </button>
        </div>
    </div>
    
    {{-- La tabla debe tener el mismo nÃºmero de <th> que de <td> --}}
    @include('pacientes.partials.table')
    
    {{-- MODALES --}}
    @include('pacientes.partials.modals.create-adulto-modal')
    @include('pacientes.partials.modals.create-adolescente-modal')
    @include('pacientes.partials.modals.create-nino-modal')
    
</div>
<script src="{{ asset('js/paciente/pacientes-modals.js') }}"></script>
@push('scripts')
<script src="{{ asset('js/pacientes.js') }}"></script>
@endpush
@endsection