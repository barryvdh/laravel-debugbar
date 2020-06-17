<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Collector for Models.
 */
class ModelsCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    public $models = [];
    public $count = 0;

    /**
     * @param Dispatcher $events
     */
    public function __construct(Dispatcher $events)
    {
        $events->listen('eloquent.retrieved:*', function ($event, $models) {
            foreach (array_filter($models) as $model) {
                $class = get_class($model);
                $this->models[$class] = ($this->models[$class] ?? 0) + 1;
                $this->count++;
            }
        });
    }

    public function collect()
    {
        ksort($this->models, SORT_NUMERIC);

        return ['data' => array_reverse($this->models), 'count' => $this->count];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'models';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return [
            "models" => [
                "icon" => "cubes",
                "widget" => "PhpDebugBar.Widgets.HtmlVariableListWidget",
                "map" => "models.data",
                "default" => "{}"
            ],
            'models:badge' => [
                'map' => 'models.count',
                'default' => 0
            ]
        ];
    }
}
