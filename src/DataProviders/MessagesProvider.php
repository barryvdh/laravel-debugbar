<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataProviders;

use DebugBar\DataCollector\MessagesCollector;

class MessagesProvider extends AbstractDataProvider
{
    public function __invoke(array $config): void
    {
        $messageCollector = new MessagesCollector();
        $this->addCollector($messageCollector);

        if ($config['trace'] ?? true) {
            $messageCollector->collectFileTrace(true);
        }

        if ($config['capture_dumps'] ?? false) {
            $originalHandler = \Symfony\Component\VarDumper\VarDumper::setHandler(function ($var) use (&$originalHandler, $messageCollector) {
                if ($originalHandler) {
                    $originalHandler($var);
                }

                $messageCollector->addMessage($var);
            });
        }
    }
}
