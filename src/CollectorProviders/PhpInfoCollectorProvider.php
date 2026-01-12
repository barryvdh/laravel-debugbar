<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\CollectorProviders;

use DebugBar\DataCollector\PhpInfoCollector;

class PhpInfoCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(array $options): void
    {
        $this->addCollector(new PhpInfoCollector());
    }
}
