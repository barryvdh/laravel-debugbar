<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Middleware;

use Closure;
use Illuminate\Http\Request;
use Fruitcake\LaravelDebugbar\LaravelDebugbar;

class DebugbarEnabled
{
    /**
     * Create a new middleware instance.
     *
     */
    public function __construct(protected LaravelDebugbar $debugbar)
    {
        $this->debugbar = $debugbar;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     */
    public function handle($request, Closure $next): mixed
    {
        if (!$this->debugbar->isEnabled()) {
            abort(404);
        }

        return $next($request);
    }
}
