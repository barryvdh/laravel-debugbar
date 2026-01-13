<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Tests;

use Fruitcake\LaravelDebugbar\Facades\Debugbar;
use Fruitcake\LaravelDebugbar\ServiceProvider;

class BrowserTestCase extends \Orchestra\Testbench\Dusk\TestCase
{
    protected static $baseServeHost = '127.0.0.1';
    protected static $baseServePort = 9292;

    /**
     * Get package providers.
     *
     *
     */
    protected function getPackageProviders(\Illuminate\Foundation\Application $app): array
    {
        return [ServiceProvider::class];
    }

    /**
     * Get package aliases.
     *
     *
     */
    protected function getPackageAliases(\Illuminate\Foundation\Application $app): array
    {
        return ['Debugbar' => Debugbar::class];
    }
}
