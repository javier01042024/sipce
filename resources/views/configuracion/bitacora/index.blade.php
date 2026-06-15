@extends('layouts.app')

@section('content')
<link href="{{ asset('css/bitacora.css') }}" rel="stylesheet">

<div class="bitacora-wrapper">
    
    @include('configuracion.bitacora.partials.header')
    @include('configuracion.bitacora.partials.stats')
    @include('configuracion.bitacora.partials.filtros')
    @include('configuracion.bitacora.partials.table')
    
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

<script src="{{ asset('js/bitacora.js') }}"></script>
@endsection