<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use Barryvdh\Debugbar\DataCollector\EventCollector;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;

class EventsCollectorCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Request $request, Dispatcher $events, array $options): void
    {
        $startTime = $request->server('REQUEST_TIME_FLOAT');
        $collectData = $options['data'] ?? false;
        $excludedEvents = $options['excluded'] ?? [];
        $eventCollector = new EventCollector($startTime, $collectData, $excludedEvents);
        $this->addCollector($eventCollector);
        $eventCollector->subscribe($events);
    }
}
