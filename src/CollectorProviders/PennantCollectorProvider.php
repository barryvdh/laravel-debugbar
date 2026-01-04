<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use Barryvdh\Debugbar\DataCollector\PennantCollector;
use Illuminate\Contracts\Foundation\Application;
use Laravel\Pennant\FeatureManager;

class PennantCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Application $app, array $options): void
    {
        if (class_exists(FeatureManager::class)
            && $app->bound(FeatureManager::class)
        ) {
            $featureManager = $app->make(FeatureManager::class);
            $this->addCollector(new PennantCollector($featureManager));
        }
    }
}
