<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Laravel\Lumen\Application;

class LumenCollector extends DataCollector implements Renderable
{
    /** @var \Laravel\Lumen\Application $app */
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

        $version = $app->version();

        return [
            "version" => substr($version, 0, strpos($version, ')') + 1),
            "environment" => $app->environment(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'lumen';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return [
            "version" => [
                "icon" => "github",
                "tooltip" => "Version",
                "map" => "lumen.version",
                "default" => ""
            ],
            "environment" => [
                "icon" => "desktop",
                "tooltip" => "Environment",
                "map" => "lumen.environment",
                "default" => ""
            ],
        ];
    }
}
