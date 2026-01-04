<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use DebugBar\Bridge\Symfony\SymfonyMailCollector;
use Illuminate\Events\Dispatcher;
use Illuminate\Mail\Events\MessageSent;

class MailCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Dispatcher $events, array $options): void
    {
        $mailCollector = new SymfonyMailCollector();
        $this->addCollector($mailCollector);

        $events->listen(function (MessageSent $event) use ($mailCollector) {
            $mailCollector->addSymfonyMessage($event->sent->getSymfonySentMessage());
        });

        if (($options['show_body'] ?? false) || ($options['full_log'] ?? false)) {
            $mailCollector->showMessageBody();
        }
        //
        //        if ($this->hasCollector('time') && ($options['timeline'] ?? false)) {
        //            // TODO; use MessageSending and MessageSent events
        //        }
    }
}
