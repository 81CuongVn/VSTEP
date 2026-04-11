<?php

declare(strict_types=1);

namespace App\Database\Connectors;

use Illuminate\Database\Connectors\PostgresConnector;

class NeonPostgresConnector extends PostgresConnector
{
    protected function addSslOptions($dsn, array $config)
    {
        $dsn = parent::addSslOptions($dsn, $config);

        $endpoint = $config['neon_endpoint'] ?? null;

        if (is_string($endpoint) && $endpoint !== '') {
            $dsn .= ";options='endpoint={$endpoint}'";
        }

        return $dsn;
    }
}
