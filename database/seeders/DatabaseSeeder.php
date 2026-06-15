<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Ejecuta la siembra de la base de datos.
     * Carga los seeders necesarios para inicializar el sistema:
     * - Roles y permisos predeterminados
     * - Usuario administrador principal
     */
    public function run(): void
    {
        // Mostrar mensaje de inicio en la consola
        $this->command->info('🚀 Iniciando siembra de la base de datos...');
        $this->command->info('');
        
        // Llamar a los seeders en orden
        $this->call([
            RoleSeeder::class,       // Primero: Crear roles y permisos del sistema
            AdminUserSeeder::class,  // Segundo: Crear usuario administrador (depende de los roles)
        ]);
        
        // Mostrar mensaje de finalización en la consola
        $this->command->info('');
        $this->command->info('✅ Base de datos sembrada correctamente!');
    }
}