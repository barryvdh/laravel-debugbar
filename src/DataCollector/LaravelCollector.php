<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\Foundation\Application;

class LaravelCollector extends DataCollector implements Renderable
{
    /** @var \Illuminate\Foundation\Application $app */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app = null)
    {
        $this->app = $app;
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        // Fallback if not injected
        $app = $this->app ?: app();

        return [
            "version" => $app::VERSION,
            "environment" => $app->environment(),
            "locale" => $app->getLocale(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'laravel';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return [
            "version" => [
                "icon" => "github",
                "tooltip" => "Laravel Version",
                "map" => "laravel.version",
                "default" => ""
            ],
            "environment" => [
                "icon" => "desktop",
                "tooltip" => "Environment",
                "map" => "laravel.environment",
                "default" => ""
            ],
            "locale" => [
                "icon" => "flag",
                "tooltip" => "Current locale",
                "map" => "laravel.locale",
                "default" => "",
            ],
        ];
    }
}
