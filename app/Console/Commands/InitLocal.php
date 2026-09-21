<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class InitLocal extends Command
{
    protected $signature = 'sipce:init-local {--force : Reejecutar incluso si la BD local ya existe}';

    protected $description = 'Crea la BD local SQLite del escritorio (migra y siembra roles + admin). Solo afecta a la conexión sqlite.';

    public function handle(): int
    {
        $dbPath = config('database.connections.sqlite.database');
        $dbPath = $dbPath === ':memory:' ? null : $dbPath;

        if ($dbPath) {
            $dir = dirname($dbPath);
            if (! is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
        }

        if (file_exists($dbPath) && ! $this->option('force')) {
            $this->warn('La BD local ya existe ('.$dbPath.'). Usa --force para recrearla desde cero.');
        }

        // Forzar la conexión local para TODO lo que se ejecute aquí
        config(['database.default' => 'sqlite']);
        DB::purge('sqlite');

        $this->line('Migrando base local (sqlite)...');
        $this->call('migrate', ['--force' => true, '--database' => 'sqlite']);

        $this->line('Sembrando roles y usuario administrador...');
        $this->call('db:seed', [
            '--class' => \Database\Seeders\DatabaseSeeder::class,
            '--force' => true,
            '--database' => 'sqlite',
        ]);

        $this->ensureStorageDirs();

        $this->newLine();
        $this->info('BD local inicializada: '.$dbPath);
        $this->line('Acceso offline: admin@example.com / password123');
        $this->line('Al primer inicio CON internet se descargan los catálogos y usuarios del servidor.');

        return self::SUCCESS;
    }

    private function ensureStorageDirs(): void
    {
        foreach ([
            'app/sync',
            'framework/cache/data',
            'framework/sessions',
            'framework/views',
            'logs',
        ] as $dir) {
            $path = storage_path($dir);
            if (! is_dir($path)) {
                mkdir($path, 0777, true);
            }
        }
    }
}