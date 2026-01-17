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
            $debugbar->startMeasure('application', 'Application', 'time');
        });
    }
}
