<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\CollectorProviders;

use Fruitcake\LaravelDebugbar\DataCollector\ConfigCollector;
use Illuminate\Config\Repository;

class ConfigCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Repository $config, array $options): void
    {
        $configCollector = new ConfigCollector($config);
        $this->addCollector($configCollector);
    }
}
