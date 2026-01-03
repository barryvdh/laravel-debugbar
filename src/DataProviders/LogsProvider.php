<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataProviders;

use Barryvdh\Debugbar\DataCollector\LogsCollector;
use DebugBar\DataCollector\MessagesCollector;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Log\Logger;

class LogsProvider extends AbstractDataProvider
{
    public function __invoke(Logger $logger, array $config): void
    {
        $file = $config['file'] ?? 'laravel.log';
        $this->addCollector(new LogsCollector($file));

    }
}
