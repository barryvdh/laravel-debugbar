<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use DebugBar\DataCollector\PhpInfoCollector;

class PhpInfoCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(array $options): void
    {
        $this->addCollector(new PhpInfoCollector());
    }
}
