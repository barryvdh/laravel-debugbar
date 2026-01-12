<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\CollectorProviders;

use Fruitcake\LaravelDebugbar\DataCollector\LogsCollector;
use Illuminate\Log\Logger;

class LogsCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Logger $logger, array $options): void
    {
        $file = $options['file'] ?? 'laravel.log';
        $this->addCollector(new LogsCollector($file));

    }
}
