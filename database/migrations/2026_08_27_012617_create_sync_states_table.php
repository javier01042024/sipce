<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_states', function (Blueprint $table) {
            $table->id();
            $table->string('device_id', 100)->comment('Identificador único del dispositivo (UUID o nombre)');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('last_sync_at')->comment('Timestamp del último sync exitoso');
            $table->unsignedInteger('records_sent')->default(0);
            $table->unsignedInteger('records_received')->default(0);
            $table->timestamps();

            $table->unique(['device_id', 'user_id']);
            $table->index('last_sync_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_states');
    }
};
