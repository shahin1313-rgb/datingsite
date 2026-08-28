<?php

namespace Tests\Support;

use Illuminate\Contracts\Foundation\Application;
use RuntimeException;

final class TestingDatabaseGuard
{
    public static function assertSafe(Application $app): void
    {
        if (! $app->environment('testing')) {
            throw new RuntimeException(
                'Tests are allowed only when APP_ENV=testing.'
            );
        }

        $connection = (string) config('database.default');
        $database = (string) config(
            "database.connections.{$connection}.database"
        );

        if ($connection === 'sqlite' && $database === ':memory:') {
            return;
        }

        $confirmation = (string) config(
            'database.testing_confirmation'
        );

        if (
            $database === '' ||
            $confirmation === '' ||
            ! hash_equals($database, $confirmation)
        ) {
            throw new RuntimeException(
                'Test database is not confirmed. In .env.testing, set '
                .'TEST_DATABASE_CONFIRMATION to the exact DB_DATABASE value.'
            );
        }
    }
}
