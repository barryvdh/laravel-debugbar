<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\CollectorProviders;

use DebugBar\Bridge\Symfony\SymfonyMailCollector;
use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Events\Dispatcher;
use Illuminate\Mail\Events\MessageSending;
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

        if (($options['show_body'] ?? true) || ($options['full_log'] ?? false)) {
            $mailCollector->showMessageBody();
        }

        if ($this->hasCollector('time') && ($options['timeline'] ?? true)) {
            /** @var TimeDataCollector $timeCollector */
            $timeCollector = $this->getCollector('time');

            $events->listen(MessageSending::class, fn(MessageSending $e) => $timeCollector->startMeasure('Mail: ' . $e->message->getSubject()));
            $events->listen(MessageSent::class, fn(MessageSent $e) => $timeCollector->stopMeasure('Mail: ' . $e->message->getSubject()));
        }
    }
}
