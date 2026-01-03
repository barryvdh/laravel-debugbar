<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataProviders;

use Barryvdh\Debugbar\DataCollector\EventCollector;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;

class EventsProvider extends AbstractDataProvider
{
    public function __invoke(Request $request, Dispatcher $events, array $config): void
    {
        $startTime = $request->server('REQUEST_TIME_FLOAT');
        $collectData = $config['data'] ?? false;
        $excludedEvents = $config['excluded'] ?? false;
        $eventCollector = new EventCollector($startTime, $collectData, $excludedEvents);
        $this->addCollector($eventCollector);
        $eventCollector->subscribe($events);
    }
}
