<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use RuntimeException;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $environment = $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? getenv('APP_ENV');
        $connection = $_ENV['DB_CONNECTION'] ?? $_SERVER['DB_CONNECTION'] ?? getenv('DB_CONNECTION');
        $database = $_ENV['DB_DATABASE'] ?? $_SERVER['DB_DATABASE'] ?? getenv('DB_DATABASE');
        $databaseUrl = $_ENV['DATABASE_URL'] ?? $_SERVER['DATABASE_URL'] ?? getenv('DATABASE_URL');

        if (
            $environment !== 'testing'
            || $connection !== 'sqlite'
            || $database !== ':memory:'
            || ! in_array($databaseUrl, [false, null, ''], true)
        ) {
            throw new RuntimeException(
                'Pruebas bloqueadas antes del arranque: las variables deben forzar testing y SQLite :memory:.'
            );
        }

        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $configuredDatabaseUrl = config('database.connections.sqlite.url');
        $usesIsolatedDatabase = $app->environment('testing')
            && config('database.default') === 'sqlite'
            && config('database.connections.sqlite.database') === ':memory:'
            && ($configuredDatabaseUrl === null || $configuredDatabaseUrl === '');

        if (! $usesIsolatedDatabase) {
            throw new RuntimeException(
                'Pruebas bloqueadas: se requiere APP_ENV=testing y SQLite :memory: sin DATABASE_URL.'
            );
        }

        return $app;
    }
}
