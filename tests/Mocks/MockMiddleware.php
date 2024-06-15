<?php

namespace Barryvdh\Debugbar\Tests\Mocks;

use Closure;

class MockMiddleware
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}