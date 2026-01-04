<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use DebugBar\DataCollector\MessagesCollector;

class MessagesCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(array $options): void
    {
        $messageCollector = new MessagesCollector();
        $this->addCollector($messageCollector);

        if ($options['trace'] ?? true) {
            $messageCollector->collectFileTrace(true);
        }

        if ($options['capture_dumps'] ?? false) {
            $originalHandler = \Symfony\Component\VarDumper\VarDumper::setHandler(function ($var) use (&$originalHandler, $messageCollector) {
                if ($originalHandler) {
                    $originalHandler($var);
                }

                $messageCollector->addMessage($var);
            });
        }
    }
}
