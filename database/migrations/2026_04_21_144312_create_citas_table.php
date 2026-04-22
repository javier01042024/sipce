<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('citas', function (Blueprint $table) {
        $table->id();

        $table->foreignId('paciente_id')
            ->constrained()
            ->onDelete('cascade');

        $table->date('fecha');

        $table->enum('estado', [
            'pendiente',
            'atendida',
            'cancelada',
            'no_asistio'
        ])->default('pendiente');

        $table->text('objetivo')->nullable();
        $table->text('planificacion')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
