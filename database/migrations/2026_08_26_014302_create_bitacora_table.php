<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('usuario_nombre', 150)->nullable();
            $table->string('accion', 50);
            $table->string('tabla_afectada', 100);
            $table->unsignedBigInteger('registro_id')->nullable();
            $table->text('datos_viejos')->nullable();
            $table->text('datos_nuevos')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('fecha_hora');

            $table->index('accion');
            $table->index('tabla_afectada');
            $table->index('fecha_hora');
            $table->index('usuario_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora');
    }
};
