@extends('layouts.app')

@section('content')
<link href="{{ asset('css/respaldos.css') }}" rel="stylesheet">

<div class="respaldos-wrapper">
    
    @include('configuracion.respaldos.partials.header')
    @include('configuracion.respaldos.partials.stats')
    @include('configuracion.respaldos.partials.config')
    @include('configuracion.respaldos.partials.table')
    
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

<script src="{{ asset('js/respaldos.js') }}"></script>
@endsection