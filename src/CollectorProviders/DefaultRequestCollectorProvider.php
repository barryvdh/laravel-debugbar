<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use DebugBar\DataCollector\RequestDataCollector;

class DefaultRequestCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(array $options): void
    {
        $this->addCollector(new RequestDataCollector());
    }
}
