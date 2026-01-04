<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use Barryvdh\Debugbar\DataCollector\ViewCollector;
use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Events\Dispatcher;

class ViewsCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Dispatcher $events, array $options): void
    {
        $collectData = $options['data'] ?? false;
        $excludePaths = $options['exclude_paths'] ?? [];
        $group = $options['group'] ?? true;

        if ($this->hasCollector('time') && ($options['timeline'] ?? false)) {
            /** @var TimeDataCollector   $timeCollector */
            $timeCollector = $this->getCollector('time');
        } else {
            $timeCollector = null;
        }

        $viewCollector = new ViewCollector($collectData, $excludePaths, $group, $timeCollector);
        $this->addCollector($viewCollector);
        $events->listen(
            'composing:*',
            function ($event, $params) use ($viewCollector) {
                $viewCollector->addView($params[0]);
            },
        );
    }
}
