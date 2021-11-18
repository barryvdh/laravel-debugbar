<?php

namespace Barryvdh\Debugbar;

use DebugBar\DataCollector\DataCollectorInterface;

/**
 * @method static LaravelDebugbar addCollector(DataCollectorInterface $collector)
 * @method static void addMessage(mixed $message, string $label = 'info')
 * @method static void alert(mixed $message)
 * @method static void critical(mixed $message)
 * @method static void debug(mixed $message)
 * @method static void emergency(mixed $message)
 * @method static void error(mixed $message)
 * @method static LaravelDebugbar getCollector(string $name)
 * @method static bool hasCollector(string $name)
 * @method static void info(mixed $message)
 * @method static void log(mixed $message)
 * @method static void notice(mixed $message)
 * @method static void warning(mixed $message)
 * @method static mixed measure(string $label, \Closure $closure)
 *
 * @deprecated Renamed to \Barryvdh\Debugbar\Facades\Debugbar
 * @see \Barryvdh\Debugbar\Facades\Debugbar
 *
 * @see \Barryvdh\Debugbar\LaravelDebugbar
 */
class Facade extends \Barryvdh\Debugbar\Facades\Debugbar
{
}
