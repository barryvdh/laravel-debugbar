<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Tests\Mocks;

use Closure;

class MockMiddleware
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
