<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnosticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('codigo_cie', 20)->nullable()->comment('Código CIE-10');
            $table->string('diagnostico', 500)->comment('Descripción del diagnóstico');
            $table->text('observaciones')->nullable();
            $table->boolean('es_principal')->default(false);
            $table->date('fecha')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosticos');
    }
};
