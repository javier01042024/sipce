@extends('layouts.app')

@section('content')
<link href="{{ asset('css/pacientes.css') }}" rel="stylesheet">

<div class="pacientes-wrapper">
    
    @include('pacientes.partials.header')
    
    @if(session('success'))
    <div class="alert-success" style="display: none;">
        {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert-error" style="display: none;">
        {{ session('error') }}
    </div>
    @endif
    
    @include('pacientes.partials.table')
    
</div>

@include('pacientes.partials.modals.create-modal')
@include('pacientes.partials.modals.delete-modal')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/pacientes.js') }}"></script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: @json(session('success')),
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        background: '#11998e',
        color: 'white'
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: @json(session('error')),
        confirmButtonColor: '#ef4444'
    });
</script>
@endif

@endsection