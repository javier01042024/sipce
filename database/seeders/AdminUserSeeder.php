<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Asignar rol de administrador si no lo tiene
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
            $this->command->info('✅ Rol de administrador asignado al usuario.');
        }

        $this->command->info('─────────────────────────────────────');
        $this->command->info('👤 USUARIO ADMINISTRADOR CREADO');
        $this->command->info('─────────────────────────────────────');
        $this->command->info("📧 Email:    admin@example.com");
        $this->command->info("🔑 Password: password123");
        $this->command->info("🆔 ID:       {$admin->id}");
        $this->command->info('─────────────────────────────────────');
    }
}