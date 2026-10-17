<?php

/**
 * Vercel serverless entry point for the GYRS Laravel application.
 *
 * Vercel Functions have a read-only filesystem except for /tmp. Laravel's
 * writable paths (views, cache, sessions, logs) and, in demo mode, the SQLite
 * database therefore live under /tmp. Configure a real Supabase Postgres
 * connection (DB_CONNECTION=pgsql + DB_*) to use durable storage instead.
 */

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$tmp = '/tmp';
$demoDatabase = $tmp.'/gyrs.sqlite';

$defaults = [
    'DB_CONNECTION' => 'sqlite',
    'DB_DATABASE' => $demoDatabase,
];

foreach ($defaults as $key => $value) {
    if (! getenv($key)) {
        putenv("{$key}={$value}");
    }

    $_ENV[$key] = getenv($key);
    $_SERVER[$key] = getenv($key);
}

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

if (getenv('DB_CONNECTION') === 'sqlite' && ! file_exists($demoDatabase)) {
    touch($demoDatabase);

    try {
        $kernel = $app->make(ConsoleKernel::class);
        $kernel->call('migrate', ['--force' => true]);
        $kernel->call('db:seed', ['--force' => true]);
    } catch (Throwable $e) {
        // A real database is configured but unreachable, or seeding failed;
        // let the HTTP layer report the underlying error.
        error_log('GYRS demo bootstrap failed: '.$e->getMessage());
    }
}

$app->handleRequest(Request::capture());
