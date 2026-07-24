<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $databaseUrl = config('database.connections.sqlite.url');

        if (
            ! app()->environment('testing')
            || config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:'
            || ! in_array($databaseUrl, [null, ''], true)
        ) {
            throw new RuntimeException('Pruebas bloqueadas: la base efectiva no es SQLite :memory:.');
        }

        Http::preventStrayRequests();
        Queue::fake();
        Storage::fake('local');
        Storage::fake('public');
    }
}
