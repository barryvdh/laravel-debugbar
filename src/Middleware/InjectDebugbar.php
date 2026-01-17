<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Fruitcake\LaravelDebugbar\LaravelDebugbar;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Throwable;

class InjectDebugbar
{
    public function __construct(protected LaravelDebugbar $debugbar) {}

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     */
    public function handle($request, Closure $next): mixed
    {
        if (!$this->debugbar->isEnabled() || $this->debugbar->requestIsExcluded($request)) {
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
        if (! app()->bound(ExceptionHandler::class) || ! $passable instanceof Request) {
            throw $e;
        }

        $handler = app(ExceptionHandler::class);

        $handler->report($e);

        return $handler->render($passable, $e);
    }
}
