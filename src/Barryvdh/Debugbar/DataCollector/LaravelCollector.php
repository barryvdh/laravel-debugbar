<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;


class LaravelCollector extends DataCollector  implements Renderable
{

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $app = app();
        return array(
            "version" => $app::VERSION,
            "environment" => $app->environment()
        );
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
        return array(
            "version" => array(
                "icon" => "github",
                "tooltip" => "Version",
                "map" => "laravel.version",
                "default" => ""
            ),
            "environment" => array(
                "icon" => "desktop",
                "tooltip" => "Environment",
                "map" => "laravel.environment",
                "default" => ""
            )
        );
    }
}
