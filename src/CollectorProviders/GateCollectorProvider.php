<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use Barryvdh\Debugbar\DataCollector\GateCollector;

class GateCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(GateCollector $gateCollector, array $options): void
    {
        $this->addCollector($gateCollector);

        if ($options['trace'] ?? false) {
            $gateCollector->collectFileTrace(true);
            $gateCollector->addBacktraceExcludePaths($options['exclude_paths'] ?? []);
        }
    }
}
