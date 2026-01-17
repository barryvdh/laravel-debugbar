<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\CollectorProviders;

class ExceptionsCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(array $options): void
    {
        $exceptionCollector = $this->debugbar->getExceptionsCollector();
        $this->addCollector($exceptionCollector);
        $exceptionCollector->setChainExceptions($options['chain'] ?? true);
    }
}
