<?php
// app/Database/NeonPgsqlConnector.php

namespace App\Database;

use Illuminate\Database\Connectors\PostgresConnector;

class NeonPgsqlConnector extends PostgresConnector
{
    /**
     * Neon exige el endpoint (primera parte del host) para conexiones
     * con clientes antiguos (libpq sin soporte SNI). Se inyecta la opción
     * options='endpoint=<id>' en el DSN cuando el host es de Neon.
     *
     * @param  array  $config
     * @return string
     */
    protected function getDsn(array $config)
    {
        $dsn = parent::getDsn($config);

        $host = $config['host'] ?? null;
        if (is_string($host)
            && str_contains($host, '.neon.tech')
            && ! str_contains($dsn, 'options=')) {
            $endpoint = strtok($host, '.');
            if ($endpoint !== false) {
                $dsn .= ";options='endpoint=" . $endpoint . "'";
            }
        }

        return $dsn;
    }
}