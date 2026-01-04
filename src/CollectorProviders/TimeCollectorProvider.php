<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\PreparingResponse;
use Illuminate\Routing\Events\ResponsePrepared;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Events\Routing;

class TimeCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Application $app, Request $request, Dispatcher $events, array $options): void
    {
        $startTime = (float) $request->server('REQUEST_TIME_FLOAT');

        if ($this->hasCollector('time')) {
            /** @var TimeDataCollector $timeCollector */
            $timeCollector = $this['time'];
        } else {
            $timeCollector = new TimeDataCollector($startTime);
            $this->addCollector($timeCollector);
        }

        if ($options['memory_usage'] ?? false) {
            $timeCollector->showMemoryUsage();
        }

        if ($startTime) {
            $app->booted(
                function () use ($startTime, $timeCollector) {
                    $timeCollector->addMeasure('Booting', $startTime, microtime(true), [], 'time');
                },
            );
        }

        $timeCollector->startMeasure('application', 'Application', 'time');

        $events->listen(Routing::class, fn() => $timeCollector->startMeasure('Routing'));
        $events->listen(RouteMatched::class, fn() => $timeCollector->stopMeasure('Routing'));

        $events->listen(PreparingResponse::class, fn() => $timeCollector->startMeasure('Preparing Response'));
        $events->listen(ResponsePrepared::class, fn() => $timeCollector->stopMeasure('Preparing Response'));
    }
}
