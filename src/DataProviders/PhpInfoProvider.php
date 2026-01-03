<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataProviders;

use DebugBar\DataCollector\PhpInfoCollector;

class PhpInfoProvider extends AbstractDataProvider
{
    public function __invoke(array $config): void
    {
        $this->addCollector(new PhpInfoCollector());
    }
}
