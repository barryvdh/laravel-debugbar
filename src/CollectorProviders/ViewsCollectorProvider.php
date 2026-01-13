<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\CollectorProviders;

use Fruitcake\LaravelDebugbar\DataCollector\ViewCollector;
use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Events\Dispatcher;

class ViewsCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Dispatcher $events, array $options): void
    {
        $collectData = $options['data'] ?? false;
        $excludePaths = $options['exclude_paths'] ?? [];
        $group = $options['group'] ?? true;

        $viewCollector = new ViewCollector($collectData, $excludePaths, $group);

        if ($this->hasCollector('time') && ($options['timeline'] ?? false)) {
            /** @var TimeDataCollector   $timeCollector */
            $timeCollector = $this->getCollector('time');
            $viewCollector->setTimeDataCollector($timeCollector);
        }

        $this->addCollector($viewCollector);
        $events->listen(
            'composing:*',
            function ($event, $params) use ($viewCollector): void {
                $viewCollector->addView($params[0]);
            },
        );
    }
}
