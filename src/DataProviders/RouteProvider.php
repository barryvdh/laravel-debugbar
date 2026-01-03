<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataProviders;

use Barryvdh\Debugbar\DataCollector\RouteCollector;

class RouteProvider extends AbstractDataProvider
{
    public function __invoke(RouteCollector $routeCollector, array $config): void
    {
        $this->addCollector($routeCollector);
    }
}
