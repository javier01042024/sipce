@extends('layouts.app')

@section('content')
<link href="{{ asset('css/citas.css') }}" rel="stylesheet">

<div class="citas-wrapper">
    
    <!-- HEADER -->
    @include('citas.partials.header')
    
    @if(isset($citasHoy) && $citasHoy->count() > 0)
        <!-- CITAS DE HOY -->
        <div class="citas-section">
            <div class="section-title hoy">
                <i class="fas fa-star"></i>
                Citas de Hoy ({{ $citasHoy->count() }})
            </div>
            <div class="citas-grid">
                @foreach($citasHoy as $cita)
                    <x-cita-card :cita="$cita" tipo="hoy" />
                @endforeach
            </div>
        </div>
    @endif
    
    @if(isset($citas) && $citas->count() > 0)
        <!-- PRÓXIMAS CITAS -->
        <div class="citas-section">
            <div class="section-title proximas">
                <i class="fas fa-calendar-check"></i>
                Próximas Citas ({{ $citas->count() }})
            </div>
            <div class="citas-grid">
                @foreach($citas as $cita)
                    <x-cita-card :cita="$cita" tipo="proxima" />
                @endforeach
            </div>
        </div>
    @endif
    
    @if(isset($citasCanceladas) && $citasCanceladas->count() > 0)
        <!-- CITAS CANCELADAS -->
        <div class="citas-section">
            <div class="section-title canceladas">
                <i class="fas fa-ban"></i>
                Citas Canceladas ({{ $citasCanceladas->count() }})
            </div>
            <div class="citas-grid">
                @foreach($citasCanceladas as $cita)
                    <x-cita-card :cita="$cita" tipo="cancelada" />
                @endforeach
            </div>
        </div>
    @endif
    
    @if(
        (!isset($citasHoy) || $citasHoy->count() === 0) && 
        (!isset($citas) || $citas->count() === 0) && 
        (!isset($citasCanceladas) || $citasCanceladas->count() === 0)
    )
        @include('citas.partials.empty-state')
    @endif
    
</div>

<!-- MODAL -->
@include('citas.partials.modals.cancel-modal')

<script src="{{ asset('js/citas.js') }}"></script>
@endsection