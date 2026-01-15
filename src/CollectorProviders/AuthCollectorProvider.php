<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\CollectorProviders;

use Fruitcake\LaravelDebugbar\DataCollector\MultiAuthCollector;

class AuthCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(array $options): void
    {
        $guards = config('auth.guards', []);
        $authCollector = new MultiAuthCollector($guards);
        $this->addCollector($authCollector);

        $authCollector->setShowName($options['show_name'] ?? false);
        $authCollector->setShowGuardsData($options['show_guards'] ?? true);
    }
}
