<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use Barryvdh\Debugbar\DataCollector\RouteCollector;

class RouteCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(RouteCollector $routeCollector, array $options): void
    {
        $this->addCollector($routeCollector);
    }
}
