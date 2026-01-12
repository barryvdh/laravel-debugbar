<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\CollectorProviders;

use Fruitcake\LaravelDebugbar\LaravelDebugbar;
use DebugBar\DataCollector\DataCollectorInterface;

abstract class AbstractCollectorProvider
{
    public function __construct(
        protected readonly LaravelDebugbar $debugbar,
    ) {}

    protected function addCollector(DataCollectorInterface $collector): void
    {
        $this->debugbar->addCollector($collector);
    }

    public function hasCollector(string $name): bool
    {
        return $this->debugbar->hasCollector($name);
    }

    public function getCollector(string $name): DataCollectorInterface
    {
        return $this->debugbar->getCollector($name);
    }

    protected function addCollectorException(string $message, \Throwable $exception)
    {
        $this->addThrowable(
            new \RuntimeException(
                $message . ' on Laravel Debugbar: ' . $exception->getMessage(),
                (int) $exception->getCode(),
                $exception,
            ),
        );
    }

    /**
     * Adds an exception to be profiled in the debug bar
     */
    public function addThrowable(\Throwable $e): void
    {
        if ($this->hasCollector('exceptions')) {
            /** @var \DebugBar\DataCollector\ExceptionsCollector $collector */
            $collector = $this->getCollector('exceptions');
            $collector->addThrowable($e);
        }
    }
}
