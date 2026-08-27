@extends('layouts.app')

@section('content')

{{-- Incluir el CSS directamente --}}
<link href="{{ asset('css/pacientes.css') }}" rel="stylesheet">

@include('pacientes.partials.edit.header')

<div class="editar-paciente-wrapper">
    @include('pacientes.partials.edit.patient-info-badge')
    @include('pacientes.partials.edit.error-alert')
    @include('pacientes.partials.edit.form')
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/pacientes.js') }}"></script>
@endpush