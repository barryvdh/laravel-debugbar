<?php namespace Barryvdh\Debugbar\Middleware;

use Closure;
use Barryvdh\Debugbar\LaravelDebugbar;

class Debugbar {

    /**
     * The Exception Handler
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $this->debugbar->modifyResponse($request, $response);

        return $response;

    }
}
