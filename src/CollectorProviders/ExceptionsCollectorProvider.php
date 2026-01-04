<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use DebugBar\DataCollector\ExceptionsCollector;

class ExceptionsCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(array $options): void
    {
        $exceptionCollector = new ExceptionsCollector();
        $this->addCollector($exceptionCollector);
        $exceptionCollector->setChainExceptions($options['chain'] ?? true);
    }
}
