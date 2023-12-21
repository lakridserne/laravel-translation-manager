<?php

namespace Lakridserne\TranslationManager\Tests;

use Lakridserne\TranslationManager\ManagerServiceProvider;
use Lakridserne\TranslationManager\TranslationServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->artisan('migrate', ['--database' => 'testing']);
    }

    protected function getPackageProviders($app)
    {
        return [ManagerServiceProvider::class, TranslationServiceProvider::class];
    }
}
