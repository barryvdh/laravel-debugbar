<?php

namespace Fruitcake\LaravelDebugbar\Support\Octane;

use Fruitcake\LaravelDebugbar\LaravelDebugbar;

class ResetDebugbar
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     */
    public function handle($event): void
    {
        if ($event->sandbox->bound(LaravelDebugbar::class)) {
            /** @var LaravelDebugbar $debugbar */
            $debugbar = $event->sandbox[LaravelDebugbar::class];
            $debugbar->reset();
            $debugbar->setApplication($event->sandbox);
        }
    }
}
