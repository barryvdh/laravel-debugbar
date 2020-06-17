<?php

namespace Barryvdh\Debugbar\Tests;

use Barryvdh\Debugbar\Facade;
use Barryvdh\Debugbar\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /** @var \Barryvdh\Debugbar\LaravelDebugbar */
    private $debugbar;

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return ['Debugbar' => Facade::class];
    }

    public function getEnvironmentSetUp($app)
    {
    }

    /**
     * Get the Laravel Debugbar instance.
     *
     * @return \Barryvdh\Debugbar\LaravelDebugbar
     */
    public function debugbar()
    {
        return $this->debugbar ?? $this->debugbar = $this->app->debugbar;
    }
}
