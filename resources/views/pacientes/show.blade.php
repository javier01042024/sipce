@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="bg-white p-6 rounded-xl shadow">

        {{-- HEADER --}}
        <div class="flex justify-between items-start mb-6">

            <div>
                <h2 class="text-2xl font-bold text-purple-600">
                    Ficha del Paciente
                </h2>

                <p class="text-gray-500 text-sm">
                    Expediente clínico SIPCE
                </p>
            </div>

            {{-- PRIORIDAD --}}
            <div>
                <span class="px-4 py-1 rounded-full text-white text-sm
                    @if($paciente->prioridad == 'Alta') bg-red-500
                    @elseif($paciente->prioridad == 'Media') bg-yellow-500
                    @else bg-green-500 @endif
                ">
                    {{ $paciente->prioridad }}
                </span>
            </div>

        </div>

        {{-- GRID INFO --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-500">Expediente</p>
                <p class="font-semibold">{{ $paciente->numero_expediente }}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-500">Cédula</p>
                <p class="font-semibold">{{ $paciente->cedula_paciente }}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-500">Nombre completo</p>
                <p class="font-semibold">{{ $paciente->nombre_completo }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-500">Teléfono</p>
                <p class="font-semibold">{{ $paciente->telefono }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-500">Email</p>
                <p class="font-semibold">{{ $paciente->email }}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-500">Fecha de nacimiento</p>
                <p class="font-semibold">{{ $paciente->fecha_nacimiento }}</p>
            </div>
            <div class="mt-6">
                <h3 class="font-bold text-gray-700 mb-2">Dirección</h3>
                <div class="bg-gray-50 p-4 rounded">
                    {{ $paciente->direccion }}
                </div>
            </div>
            <div class="mt-6">
                <h3 class="font-bold text-gray-700 mb-2">Diagnóstico preliminar</h3>
                <div class="bg-gray-50 p-4 rounded">
                    {{ $paciente->diagnostico_preliminar ?? 'Sin diagnóstico registrado' }}
                </div>
            </div>
        </div>

        {{-- MOTIVO --}}
        <div class="mt-6">
            <h3 class="font-bold text-gray-700 mb-2">Motivo de consulta</h3>
            <div class="bg-gray-50 p-4 rounded">
                {{ $paciente->motivo_consulta }}
            </div>
        </div>

        {{-- DIAGNOSTICO --}}
        <div class="mt-6">
            <h3 class="font-bold text-gray-700 mb-2">Diagnóstico preliminar</h3>
            <div class="bg-gray-50 p-4 rounded">
                {{ $paciente->diagnostico_preliminar }}
            </div>
        </div>

        {{-- ACCIONES --}}
        <div class="mt-6 flex gap-3">

            <a href="{{ route('pacientes.edit', $paciente) }}"
                class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                Editar
            </a>

            <a href="{{ route('pacientes.index') }}"
                class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                Volver
            </a>

        </div>

    </div>

</div>

@endsection