@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link href="{{ asset('css/usuarios.css') }}" rel="stylesheet">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="usuarios-wrapper">
    
    @include('configuracion.usuarios.partials.header')
    @include('configuracion.usuarios.partials.stats')
    @include('configuracion.usuarios.partials.table')
    
</div>

@include('configuracion.usuarios.partials.modals.user-modal')
@include('configuracion.usuarios.partials.modals.role-modal')

<script src="{{ asset('js/usuarios.js') }}"></script>
@endsection