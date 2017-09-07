<?php namespace Barryvdh\Debugbar\Middleware;

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
     * @param  LaravelDebugbar $debugbar
     */
    public function __construct(LaravelDebugbar $debugbar)
    {
        $this->debugbar = $debugbar;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$this->debugbar->isEnabled()) {
            abort(404);
        }

        return $next($request);

    }
}
