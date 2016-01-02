<?php namespace Barryvdh\Debugbar\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Barryvdh\Debugbar\LaravelDebugbar;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;

class Debugbar
{
    /**
     * The App container
     *
     * @var Container
     */
    protected $container;

    /**
     * The DebugBar instance
     *
     * @var LaravelDebugbar
     */
    protected $debugbar;

    /**
     * Create a new middleware instance.
     *
     * @param  Container $container
     * @param  LaravelDebugbar $debugbar
     */
    public function __construct(Container $container, LaravelDebugbar $debugbar)
    {
        $this->container = $container;
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
        try {
            /** @var \Illuminate\Http\Response $response */
            $response = $next($request);
        } catch (Exception $e) {
            $response = $this->handleException($request, $e);
        }

        // Modify the response to add the Debugbar
        $this->debugbar->modifyResponse($request, $response);

        return $response;

    }

    /**
     * Handle the given exception.
     *
     * (Copy from Illuminate\Routing\Pipeline by Taylor Otwell)
     *
     * @param $passable
     * @param  Exception $e
     * @return mixed
     * @throws Exception
     */
    protected function handleException($passable, Exception $e)
    {
        if (! $this->container->bound(ExceptionHandler::class) || ! $passable instanceof Request) {
            throw $e;
        }

        $handler = $this->container->make(ExceptionHandler::class);

        $handler->report($e);

        return $handler->render($passable, $e);
    }
}
