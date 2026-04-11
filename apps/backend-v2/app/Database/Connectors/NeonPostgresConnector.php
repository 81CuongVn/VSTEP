<?php

declare(strict_types=1);

namespace App\Database\Connectors;

use Illuminate\Database\Connectors\PostgresConnector;

class NeonPostgresConnector extends PostgresConnector
{
    protected function addSslOptions($dsn, array $config)
    {
        $dsn = parent::addSslOptions($dsn, $config);

        $endpoint = $this->resolveNeonEndpoint($config);

        if (is_string($endpoint) && $endpoint !== '') {
            $dsn .= ";options='endpoint={$endpoint}'";
        }

        return $dsn;
    }

    private function resolveNeonEndpoint(array $config): ?string
    {
        $endpoint = $config['neon_endpoint'] ?? null;

        if (is_string($endpoint) && $endpoint !== '') {
            return $endpoint;
        }

        $host = $config['host'] ?? null;

        if (! is_string($host) || $host === '' || ! str_contains($host, '.neon.tech')) {
            return null;
        }

        $parts = explode('.', $host);

        return $parts[0] ?? null;
    }
}
