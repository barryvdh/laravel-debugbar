<?php

namespace Barryvdh\Debugbar\Facades;

use DebugBar\DataCollector\DataCollectorInterface;

/**
 * @method static LaravelDebugbar addCollector(DataCollectorInterface $collector)
 * @method static void addMessage(mixed $message, string $label = 'info')
 * @method static LaravelDebugbar alert(mixed $message)
 * @method static LaravelDebugbar critical(mixed $message)
 * @method static LaravelDebugbar debug(mixed $message)
 * @method static LaravelDebugbar emergency(mixed $message)
 * @method static LaravelDebugbar error(mixed $message)
 * @method static LaravelDebugbar getCollector(string $name)
 * @method static bool hasCollector(string $name)
 * @method static LaravelDebugbar info(mixed $message)
 * @method static LaravelDebugbar log(mixed $message)
 * @method static LaravelDebugbar notice(mixed $message)
 * @method static LaravelDebugbar warning(mixed $message)
 *
 * @see \Barryvdh\Debugbar\LaravelDebugbar
 */
class Debugbar extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Barryvdh\Debugbar\LaravelDebugbar::class;
    }
}
