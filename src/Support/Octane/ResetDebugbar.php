<?php

namespace Fruitcake\LaravelDebugbar\Support\Octane;

use Fruitcake\LaravelDebugbar\LaravelDebugbar;
use Laravel\Octane\Events\RequestReceived;

class ResetDebugbar
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     */
    public function handle(RequestReceived $event): void
    {
        if (! $event->sandbox->resolved(LaravelDebugbar::class)) {
            return;
        }

        with($event->sandbox->make(LaravelDebugbar::class), function (LaravelDebugbar $debugbar) use ($event) {
            $debugbar->setApplication($event->sandbox);
            $debugbar->reset();
        });
    }
}
