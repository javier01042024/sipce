<?php

namespace App\Console\Commands;

use App\Services\SyncService;
use Illuminate\Console\Command;

class SyncCommand extends Command
{
    protected $signature = 'sipce:sync
        {--loop : Ejecuta en bucle continuo (usado por el launcher del escritorio)}
        {--every=20 : Segundos entre ciclos en modo --loop}
        {--no-lock : Fuerza ejecución aunque haya otro sync en curso}';

    protected $description = 'Sincroniza la BD local (SQLite) con el servidor SIPCE (sube cola y baja cambios).';

    public function handle(SyncService $service): int
    {
        $loop = (bool) $this->option('loop');
        $every = max(3, (int) $this->option('every'));

        do {
            try {
                $result = $service->run();
                $this->line(json_encode($result));
            } catch (\Throwable $e) {
                $this->warn('Sync error: '.$e->getMessage());
            }

            if ($loop) {
                sleep($every);
            }
        } while ($loop);

        return self::SUCCESS;
    }
}