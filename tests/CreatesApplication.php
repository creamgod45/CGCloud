<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->loadEnvironmentFrom('.env.testing');

        $frameworkPath = storage_path('framework');
        @mkdir("$frameworkPath/cache/data", 0777, true);
        @mkdir("$frameworkPath/views", 0777, true);

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
