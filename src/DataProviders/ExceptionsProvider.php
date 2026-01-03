<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataProviders;

use DebugBar\DataCollector\ExceptionsCollector;

class ExceptionsProvider extends AbstractDataProvider
{
    public function __invoke(array $config): void
    {
        $exceptionCollector = new ExceptionsCollector();
        $this->addCollector($exceptionCollector);
        $exceptionCollector->setChainExceptions($config['chain'] ?? true);
    }
}
