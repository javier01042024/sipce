<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateApiToken extends Command
{
    protected $signature = 'sanctum:generate-token {email} {--name=sipce-app}';
    protected $description = 'Generar un token API Sanctum para un usuario existente';

    public function handle(): int
    {
        $email = $this->argument('email');
        $name = $this->option('name');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No se encontró usuario con email: {$email}");
            return 1;
        }

        $token = $user->createToken($name)->plainTextToken;

        $this->info("Token generado para {$user->name} ({$email}):");
        $this->newLine();
        $this->line($token);
        $this->newLine();
        $this->warn("Guarde este token. No se mostrará nuevamente.");

        return 0;
    }
}
