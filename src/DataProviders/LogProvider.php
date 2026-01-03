<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataProviders;

use DebugBar\DataCollector\MessagesCollector;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Log\Logger;

class LogProvider extends AbstractDataProvider
{
    public function __invoke(Logger $logger, array $config): void
    {
        $logCollector = new MessagesCollector('log');

        if ($this->hasCollector('messages')) {
            /** @var MessagesCollector $messagesCollector */
            $messagesCollector = $this->getCollector('messages');
            $messagesCollector->aggregate($logCollector);
        } else {
            $this->addCollector($logCollector);
        }

        $logger->listen(
            function (MessageLogged $log) use ($logCollector) {
                try {
                    $logMessage = (string) $log->message;
                    if (mb_check_encoding($logMessage, 'UTF-8')) {
                        $context = $log->context;
                        $logMessage .= (!empty($context) ? ' ' . json_encode($context, JSON_PRETTY_PRINT) : '');
                    } else {
                        $logMessage = "[INVALID UTF-8 DATA]";
                    }
                } catch (\Throwable $e) {
                    $logMessage = "[Exception: " . $e->getMessage() . "]";
                }
                $logCollector->addMessage(
                    '[' . date('H:i:s') . '] ' . "LOG.{$log->level}: " . $logMessage,
                    $log->level,
                    false,
                );
            },
        );

    }
}
