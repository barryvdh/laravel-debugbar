<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Illuminate\Support\Facades\Config;
use Laravel\Pennant\FeatureManager;

class PennantCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    protected FeatureManager $manager;

    /**
     * Create a new SessionCollector
     *
     */
    public function __construct(FeatureManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(): array
    {
        $store = $this->manager->store(Config::get('pennant.default'));

        return $store->values($store->stored());
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
