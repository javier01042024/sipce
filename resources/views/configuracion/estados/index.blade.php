{{-- resources/views/configuracion/estados/index.blade.php --}}
@extends('layouts.app')

@section('content')

@push('styles')
<link href="{{ asset('css/estados.css') }}" rel="stylesheet">
@endpush

<div class="estados-container">

    @include('configuracion.estados.partials.header')
    @include('configuracion.estados.partials.alerts')
    @include('configuracion.estados.partials.table')

</div>

@include('configuracion.estados.partials.modals.create')
@include('configuracion.estados.partials.modals.edit')
@include('configuracion.estados.partials.modals.show')

<form id="formEliminar" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
const URL_BASE = '{{ route("configuracion.estados.index") }}';
</script>
<script src="{{ asset('js/estados.js') }}"></script>
@endpush

@endsection
