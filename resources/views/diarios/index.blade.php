{{-- resources/views/diarios/index.blade.php --}}
@extends('layouts.app')

@section('content')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/diarios.css') }}">
<link rel="stylesheet" href="{{ asset('css/listado-controles.css') }}">
@endpush

<div class="diario-wrapper">

    <!-- HEADER -->
    @include('diarios.partials.header')

    <!-- ESTADÍSTICAS -->
    @include('diarios.partials.stats')

    <!-- FILTROS -->
    @include('diarios.partials.filtros')

    <!-- LISTA DE DIARIOS -->
    @if($diarios->count() > 0)
        <div class="diarios-grid">
            @foreach($diarios as $diario)
                @include('diarios.partials.card', ['diario' => $diario])
            @endforeach
        </div>

        <div class="diarios-vacio" id="diariosVacio">
            <i class="fas fa-search-minus"></i>
            <p>No se encontraron registros con los filtros aplicados</p>
        </div>
    @else
        @include('diarios.partials.empty-state')
    @endif

</div>

@push('scripts')
<script src="{{ asset('js/diarios.js') }}"></script>
@endpush

@endsection
