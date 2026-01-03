<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataProviders;

use DebugBar\DataCollector\MemoryCollector;

class MemoryProvider extends AbstractDataProvider
{
    public function __invoke(array $config): void
    {
        $memoryCollector = new MemoryCollector();
        $this->addCollector($memoryCollector);
        $memoryCollector->setPrecision($config['precision'] ?? 0);

        if (function_exists('memory_reset_peak_usage') && ($config['reset_peak_usage'] ?? false)) {
            memory_reset_peak_usage();
        }

        if ($config['with_baseline'] ?? false) {
            $memoryCollector->resetMemoryBaseline();
        }
    }
}
