<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Illuminate\Support\Facades\Config;

class PennantCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    /** @var  \Laravel\Pennant\FeatureManager */
    protected $manager;

    /**
     * Create a new SessionCollector
     *
     * @param \Laravel\Pennant\FeatureManager $manager
     */
    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(): mixed
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
                "default" => "{}"
            ]
        ];
    }
}
