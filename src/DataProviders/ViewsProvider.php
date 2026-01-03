<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataProviders;

use Barryvdh\Debugbar\DataCollector\ViewCollector;
use Illuminate\Contracts\Events\Dispatcher;

class ViewsProvider extends AbstractDataProvider
{
    public function __invoke(Dispatcher $events, array $config): void
    {
        $collectData = $config['data'] ?? false;
        $excludePaths = $config['exclude_paths'] ?? [];
        $group = $config['group'] ?? true;

        if ($this->hasCollector('time') && ($config['timeline'] ?? false)) {
            $timeCollector = $this['time'];
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
