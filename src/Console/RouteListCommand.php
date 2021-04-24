<?php

namespace Barryvdh\Debugbar\Console;

use Barryvdh\Debugbar\LaravelDebugbar;
use Illuminate\Foundation\Console\RouteListCommand as Command;
use Illuminate\Support\Str;

class RouteListCommand extends Command
{
    protected function filterRoute(array $route)
    {
        if (
            config('debugbar.hide_routes', false) &&
            Str::contains($route['uri'], config('debugbar.route_prefix'))
        ) {
            return;
        }
        return parent::filterRoute($route);
    }
}
