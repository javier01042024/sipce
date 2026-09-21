<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_pending', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique()->comment('UUID del registro original');
            $table->string('tabla', 100)->index();
            $table->string('accion', 20)->comment('CREATE, UPDATE, DELETE');
            $table->json('datos')->nullable()->comment('Snapshot del registro completo');
            $table->string('device_id', 100)->comment('Dispositivo que originó el cambio');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamp('created_local')->comment('Fecha local cuando se hizo el cambio');
            $table->boolean('sincronizado')->default(false)->index();
            $table->timestamp('sincronizado_at')->nullable();
            $table->text('error_sync')->nullable();
            $table->timestamps();

            $table->index(['sincronizado', 'tabla']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_pending');
    }
};
