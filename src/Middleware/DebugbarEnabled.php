<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Middleware;

use Closure;
use Illuminate\Http\Request;
use Fruitcake\LaravelDebugbar\LaravelDebugbar;

class DebugbarEnabled
{
    /**
     * The DebugBar instance
     *
     * @var LaravelDebugbar
     */
    protected $debugbar;

    /**
     * Create a new middleware instance.
     *
     */
    public function __construct(LaravelDebugbar $debugbar)
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

        //        logger('handle debugbar enabled middleware');

        return $next($request);
    }
}
