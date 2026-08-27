@extends('layouts.app')

@section('content')
<link href="{{ asset('css/pacientes.css') }}" rel="stylesheet">
<link href="{{ asset('css/pacientes-show.css') }}" rel="stylesheet">
<link href="{{ asset('css/pacientes-tabs.css') }}" rel="stylesheet">

<div class="ficha-paciente-wrapper">
    
    @include('pacientes.partials.show.header')
    
    {{-- Sistema de Pestañas --}}
    @include('pacientes.partials.show.tabs')
    
    {{-- Contenido de las Pestañas --}}
    <div class="tabs-content">
        {{-- Pestaña 1: Datos del Paciente --}}
        <div id="tab-datos" class="tab-panel active">
            @include('pacientes.partials.show.info-card')
            @include('pacientes.partials.show.historial-citas')
        </div>
        
        {{-- Pestaña 2: Sesiones --}}
        @include('pacientes.partials.show.tab-sesiones')
        
        {{-- Pestaña 3: Notas y Seguimiento --}}
        @include('pacientes.partials.show.tab-notas')
        
        {{-- Pestaña 4: Diario del Paciente --}}
        @include('pacientes.partials.show.tab-diario')
    </div>

    @include('pacientes.partials.show.diagnostico-modal')
    
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/pacientes-tabs.js') }}"></script>
@endpush