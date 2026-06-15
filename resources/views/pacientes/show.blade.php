@extends('layouts.app')

@section('content')
<link href="{{ asset('css/pacientes.css') }}" rel="stylesheet">
<link href="{{ asset('css/pacientes-show.css') }}" rel="stylesheet">

<div class="ficha-paciente-wrapper">
    
    @include('pacientes.partials.show.header')
    @include('pacientes.partials.show.info-card')
    @include('pacientes.partials.show.historial-citas')
    
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

@endsection