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
    public function __invoke(Dispatcher $events, array $options): void
    {

        $httpClientCollector = new HttpClientCollector();
        $masked = $options['masked'] ?? [];
        $httpClientCollector->addMaskedKeys($masked);
        $this->addCollector($httpClientCollector);

        $events->listen(ResponseReceived::class, fn(ResponseReceived $e) => $httpClientCollector->addEvent($e));
        $events->listen(ConnectionFailed::class, fn(ConnectionFailed $e) => $httpClientCollector->addEvent($e));

        if ($this->hasCollector('time') && ($options['timeline'] ?? true)) {
            /** @var TimeDataCollector $timeCollector */
            $timeCollector = $this->getCollector('time');

            $events->listen(RequestSending::class, fn(RequestSending $e) => $timeCollector->startMeasure($this->label($e->request), null, 'http'));
            $events->listen(ResponseReceived::class, fn(ResponseReceived $e) => $timeCollector->stopMeasure($this->label($e->request)));
            $events->listen(ConnectionFailed::class, fn(ConnectionFailed $e) => $timeCollector->stopMeasure($this->label($e->request)));
        }
    }

    protected function label(Request $request): string
    {
        return $request->method() . ' ' . $request->url();
    }
}
