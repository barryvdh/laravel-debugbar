<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use Barryvdh\Debugbar\DataCollector\ConfigCollector;
use Illuminate\Config\Repository;

class ConfigCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Repository $config, array $options): void
    {
        $configCollector = new ConfigCollector($config);
        $this->addCollector($configCollector);
    }
}
