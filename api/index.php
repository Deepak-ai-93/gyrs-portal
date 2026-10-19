<?php

/**
 * Vercel serverless entry point for the GYRS Laravel application.
 *
 * Vercel Functions have a read-only filesystem except for /tmp. Laravel's
 * writable paths (views, cache, sessions, logs) and the SQLite database
 * therefore live under /tmp. Configure a real Supabase Postgres connection
 * (DB_CONNECTION=pgsql + DB_*) to use durable storage instead.
 *
 * `database/database.sqlite` ships with the project and already contains the
 * fully seeded demo data, so it is copied into /tmp on boot. If it is not
 * available (or the deployment is not using SQLite) the database is built by
 * running the migrations and seeders instead.
 */

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$tmp = '/tmp';
$demoDatabase = $tmp.'/gyrs.sqlite';
$seedSource = __DIR__.'/../database/database.sqlite';

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

if (getenv('DB_CONNECTION') === 'sqlite') {
    // Refresh the ephemeral /tmp copy whenever the deployed seed database
    // changes (i.e. on every new deployment) or when it does not exist yet.
    $stamp = is_file($seedSource)
        ? (string) filemtime($seedSource).':'.(string) filesize($seedSource)
        : null;
    $stampFile = $tmp.'/gyrs-db.stamp';
    $storedStamp = is_file($stampFile) ? (string) file_get_contents($stampFile) : null;

    $needsBootstrap = ! file_exists($demoDatabase)
        || ($stamp !== null && $storedStamp !== $stamp);

    if ($needsBootstrap) {
        $seeded = false;

        if (is_file($seedSource)) {
            $staged = $demoDatabase.'.tmp';

            if (@copy($seedSource, $staged)) {
                $seeded = @rename($staged, $demoDatabase);

                if (! $seeded) {
                    @unlink($staged);
                }
            }
        }

        if (! $seeded) {
            if (! file_exists($demoDatabase)) {
                touch($demoDatabase);
            }

            try {
                $kernel = $app->make(ConsoleKernel::class);
                $kernel->call('migrate', ['--force' => true]);
                $kernel->call('db:seed', ['--force' => true]);
            } catch (Throwable $e) {
                // A real database is configured but unreachable, or seeding
                // failed; let the HTTP layer report the underlying error.
                error_log('GYRS demo bootstrap failed: '.$e->getMessage());
            }
        }

        if ($stamp !== null) {
            @file_put_contents($stampFile, $stamp);
        }
    }
}

$app->handleRequest(Request::capture());
