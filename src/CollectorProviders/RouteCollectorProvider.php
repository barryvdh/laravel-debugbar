<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\CollectorProviders;

use Fruitcake\LaravelDebugbar\DataCollector\RouteCollector;

class RouteCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(RouteCollector $routeCollector, array $options): void
    {
        $this->addCollector($routeCollector);
    }
}
