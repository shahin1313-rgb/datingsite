<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Tests\Support\TestingDatabaseGuard;

putenv('APP_ENV=testing');
$_ENV['APP_ENV'] = 'testing';
$_SERVER['APP_ENV'] = 'testing';

require dirname(__DIR__, 2).'/vendor/autoload.php';

$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

TestingDatabaseGuard::assertSafe($app);

$exitCode = Artisan::call('migrate', [
    '--force' => true,
    '--no-interaction' => true,
]);

fwrite(STDOUT, Artisan::output());

exit($exitCode);
