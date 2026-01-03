<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataProviders;

use DebugBar\DataCollector\RequestDataCollector;

class DefaultRequestProvider extends AbstractDataProvider
{
    public function __invoke(array $config): void
    {
        $this->addCollector(new RequestDataCollector());
    }
}
