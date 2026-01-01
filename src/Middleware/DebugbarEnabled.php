<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\Middleware;

use Closure;
use Illuminate\Http\Request;
use Barryvdh\Debugbar\LaravelDebugbar;

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
    public function handle($request, Closure $next)
    {
        if (!$this->debugbar->isEnabled()) {
            abort(404);
        }

        return $next($request);
    }
}
