<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * @deprecated in favor of \DebugBar\DataCollector\ObjectCountCollector
 */
class JobsCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    public $jobs = [];
    public $count = 0;

    /**
     * @param Dispatcher $events
     */
    public function __construct(Dispatcher $events)
    {
        $events->listen(\Illuminate\Queue\Events\JobQueued::class, function ($event) {
            $class = get_class($event->job);
            $this->jobs[$class] = ($this->jobs[$class] ?? 0) + 1;
            $this->count++;
        });
    }

    public function collect()
    {
        ksort($this->jobs, SORT_NUMERIC);

        return ['data' => array_reverse($this->jobs), 'count' => $this->count];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'jobs';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return [
            "jobs" => [
                "icon" => "briefcase",
                "widget" => "PhpDebugBar.Widgets.HtmlVariableListWidget",
                "map" => "jobs.data",
                "default" => "{}"
            ],
            'jobs:badge' => [
                'map' => 'jobs.count',
                'default' => 0
            ]
        ];
    }
}
