<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Support\Octane;

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

            if ($debugbar->isEnabled() && !$debugbar->requestIsExcluded($event->request)) {
                $debugbar->boot();
            }

            if ($requestStartTime = $event->request->server->get('REQUEST_TIME_FLOAT')) {
                $debugbar->getTimeCollector()->setRequestStartTime((float) $requestStartTime);
            }
            $debugbar->startMeasure('application', 'Application', 'time');
        });
    }
}
