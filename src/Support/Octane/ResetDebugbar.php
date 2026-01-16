<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Support\Octane;

use DebugBar\DataCollector\TimeDataCollector;
use Fruitcake\LaravelDebugbar\LaravelDebugbar;
use Laravel\Octane\Events\RequestReceived;

class ResetDebugbar
{
    /**
     * Handle the event.
     *
     */
    public function handle(RequestReceived $event): void
    {
        if (! $event->sandbox->resolved(LaravelDebugbar::class)) {
            return;
        }

        with($event->sandbox->make(LaravelDebugbar::class), function (LaravelDebugbar $debugbar) use ($event): void {
            $debugbar->setApplication($event->sandbox);
            $debugbar->reset();

            // Reset the time collector
            if ($debugbar->hasCollector('time')) {
                /** @var TimeDataCollector $timeCollector */
                $timeCollector = $debugbar->getCollector('time');

                $startTime = (float) $event->request->server('REQUEST_TIME_FLOAT');
                if ($startTime) {
                    $event->sandbox->booted(function () use ($startTime, $timeCollector): void {
                        $timeCollector->addMeasure('Booting', $startTime, microtime(true), [], 'time');
                        $timeCollector->startMeasure('application', 'Application', 'time');
                    });
                }
            }
        });
    }
}
