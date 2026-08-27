<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acompanantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->string('nombre', 150)->comment('Nombre completo del acompañante');
            $table->string('parentesco', 50)->comment('Padre, Madre, Tutor, etc.');
            $table->string('telefono', 15)->nullable();
            $table->string('cedula', 20)->nullable();
            $table->boolean('es_principal')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('sesiones', function (Blueprint $table) {
            $table->foreignId('acompanante_id')->nullable()->after('paciente_id')->constrained('acompanantes')->nullOnDelete();
        });

        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('municipio', 100)->nullable()->after('tipo_atencion');
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn('municipio');
        });
        Schema::table('sesiones', function (Blueprint $table) {
            $table->dropForeign(['acompanante_id']);
            $table->dropColumn('acompanante_id');
        });
        Schema::dropIfExists('acompanantes');
    }
};
