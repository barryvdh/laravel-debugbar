<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Illuminate\Support\Facades\Config;
use Laravel\Pennant\Feature;
use Laravel\Pennant\FeatureManager;

class PennantCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    /**
     * {@inheritdoc}
     */
    public function collect(): array
    {
        return Feature::all();
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'pennant';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets(): array
    {
        return [
            "pennant" => [
                "icon" => "flag",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "pennant",
                "default" => "{}",
            ],
        ];
    }
}
