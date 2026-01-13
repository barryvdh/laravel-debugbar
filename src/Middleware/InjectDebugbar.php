<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Fruitcake\LaravelDebugbar\LaravelDebugbar;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Throwable;

class InjectDebugbar
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
     * The URIs that should be excluded.
     *
     * @var array
     */
    protected $except = [];

    /**
     * Create a new middleware instance.
     *
     */
    public function __construct(Container $container, LaravelDebugbar $debugbar)
    {
        $this->container = $container;
        $this->debugbar = $debugbar;
        $this->except = config('debugbar.except') ?: [];
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     */
    public function handle($request, Closure $next): mixed
    {
        if (!$this->debugbar->isEnabled() || $this->inExceptArray($request)) {
            return $next($request);
        }

        $this->debugbar->boot();

        try {
            /** @var \Illuminate\Http\Response $response */
            $response = $next($request);
        } catch (Throwable $e) {
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
     * @param Throwable $e
     *
     * @throws Exception
     */
    protected function handleException($passable, $e): \Symfony\Component\HttpFoundation\Response
    {
        if (! $this->container->bound(ExceptionHandler::class) || ! $passable instanceof Request) {
            throw $e;
        }

        $handler = $this->container->make(ExceptionHandler::class);

        $handler->report($e);

        return $handler->render($passable, $e);
    }

    /**
     * Determine if the request has a URI that should be ignored.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function inExceptArray($request): bool
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
