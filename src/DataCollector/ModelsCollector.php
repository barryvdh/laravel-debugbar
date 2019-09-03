<?php

namespace Barryvdh\Debugbar\DataCollector;

use Barryvdh\Debugbar\DataFormatter\SimpleFormatter;
use DebugBar\DataCollector\MessagesCollector;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Str;

/**
 * Collector for Models.
 */
class ModelsCollector extends MessagesCollector
{
    public $models = [];

    /**
     * @param Dispatcher $events
     */
    public function __construct(Dispatcher $events)
    {
        parent::__construct('models');
        $this->setDataFormatter(new SimpleFormatter());

        $events->listen('eloquent.*', function ($event, $models) {
            if (Str::contains($event, 'eloquent.retrieved')) {
                foreach (array_filter($models) as $model) {
                    try {
                        $class = get_class($model);
                        $this->models[$class] = ($this->models[$class] ?? 0) + 1;
                    } catch (\ErrorException $exception) {
                        // eloquent model retrieved can be null
                    }
                }
            }
        });
    }

    public function collect()
    {
        foreach ($this->models as $type => $count) {
            $this->addMessage($count, $type);
        }

        return [
            'count' => array_sum($this->models),
            'messages' => $this->getMessages(),
        ];
    }

    public function getWidgets()
    {
        $widgets = parent::getWidgets();
        $widgets['models']['icon'] = 'cubes';

        return $widgets;
    }
}
