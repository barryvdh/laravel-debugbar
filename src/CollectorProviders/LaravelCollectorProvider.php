<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\CollectorProviders;

use Fruitcake\LaravelDebugbar\DataCollector\LaravelCollector;

class LaravelCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(array $options): void
    {
        $this->addCollector(new LaravelCollector());
    }
}
