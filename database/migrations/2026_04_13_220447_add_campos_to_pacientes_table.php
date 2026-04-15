<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
   public function up()
{
    Schema::table('pacientes', function (Blueprint $table) {
        $table->string('numero_expediente')->unique()->after('id');
        $table->string('telefono')->after('fecha_nacimiento');
        $table->string('email')->after('telefono');
        $table->text('direccion')->after('email');
    });
}

public function down()
{
    Schema::table('pacientes', function (Blueprint $table) {
        $table->dropColumn([
            'numero_expediente',
            'telefono',
            'email',
            'direccion'
        ]);
    });
}
};
