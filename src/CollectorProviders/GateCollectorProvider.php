<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\CollectorProviders;

use Fruitcake\LaravelDebugbar\DataCollector\GateCollector;
use Illuminate\Support\Facades\Gate;

class GateCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(array $options): void
    {
        $gateCollector = new GateCollector('gate');
        $this->addCollector($gateCollector);

        if ($options['trace'] ?? false) {
            $gateCollector->collectFileTrace(true);
            $gateCollector->addBacktraceExcludePaths($options['exclude_paths'] ?? []);
        }

        Gate::after(fn ($user, $ability, $result, $arguments = []) => $gateCollector->addCheck($user, $ability, $result, $arguments)) ;
    }
}
