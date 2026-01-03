<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataProviders;

use DebugBar\Bridge\Symfony\SymfonyMailCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\RawMessage;

class MailProvider extends AbstractDataProvider
{
    public function __invoke(Dispatcher $events, array $config): void
    {
        $mailCollector = new SymfonyMailCollector();
        $this->addCollector($mailCollector);

        $events->listen(function (MessageSent $event) use ($mailCollector) {
            $mailCollector->addSymfonyMessage($event->sent->getSymfonySentMessage());
        });

        if (($config['show_body'] ?? false) || ($config['full_log'] ?? false)) {
            $mailCollector->showMessageBody();
        }

        if ($this->hasCollector('time') && ($config['timeline'] ?? false)) {
            // TODO; use MessageSending and MessageSent events
        }
    }
}
