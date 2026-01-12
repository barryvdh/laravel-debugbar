<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\CollectorProviders;

use DebugBar\DataCollector\MemoryCollector;

class MemoryCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(array $options): void
    {
        $memoryCollector = new MemoryCollector();
        $this->addCollector($memoryCollector);
        $memoryCollector->setPrecision($options['precision'] ?? 0);

        if (function_exists('memory_reset_peak_usage') && ($options['reset_peak_usage'] ?? false)) {
            memory_reset_peak_usage();
        }

        if ($options['with_baseline'] ?? false) {
            $memoryCollector->resetMemoryBaseline();
        }
    }
}
