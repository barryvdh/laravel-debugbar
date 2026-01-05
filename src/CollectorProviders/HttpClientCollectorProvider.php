<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use Barryvdh\Debugbar\DataCollector\HttpClientCollector;
use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Client\Events\ConnectionFailed;
use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Client\Request;

class HttpClientCollectorProvider extends AbstractCollectorProvider
{
    protected ?HttpClientCollector $httpClientCollector = null;

    public function __invoke(Dispatcher $events, array $options): void
    {
        if ($this->hasCollector('time') && ($options['timeline'] ?? true)) {
            /** @var TimeDataCollector $timeCollector */
            $timeCollector = $this->getCollector('time');
        } else {
            $timeCollector = null;
        }

        $httpClientCollector = new HttpClientCollector('http_client', $timeCollector);
        $this->httpClientCollector = $httpClientCollector;

        $masked = $options['masked'] ?? [];
        $httpClientCollector->addMaskedKeys($masked);

        $this->addCollector($httpClientCollector);


        $events->listen(ResponseReceived::class, fn(ResponseReceived $e) => $this->addEvent($e));
        $events->listen(ConnectionFailed::class, fn(ConnectionFailed $e) => $this->addEvent($e));
    }

    protected function addEvent(ResponseReceived|ConnectionFailed $event): void
    {
        try {
            $this->httpClientCollector->addEvent($event);
        } catch (\Throwable $e) {
            $this->addThrowable($e);
        }
    }
}
