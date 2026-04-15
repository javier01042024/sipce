<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('cedula_paciente');
            $table->string('nombre_completo', 100);
            $table->date('fecha_nacimiento');
            $table->text('motivo_consulta');
            $table->text('diagnostico_preliminar');
            $table->enum('prioridad', ['Alta', 'Media', 'Baja']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
