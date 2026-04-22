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
    Schema::create('diarios', function (Blueprint $table) {
        $table->id();

        $table->foreignId('paciente_id')
            ->constrained()
            ->onDelete('cascade');

        $table->date('fecha');

        $table->text('contenido');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diario_pacientes');
    }
};
