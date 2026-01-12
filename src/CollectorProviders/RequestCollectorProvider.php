<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\CollectorProviders;

use Fruitcake\LaravelDebugbar\DataCollector\RequestCollector;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\ResponsePrepared;

class RequestCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Repository $config, Dispatcher $events, Request $request, array $options): void
    {
        $sessionHiddens = (array) $config->get('debugbar.options.session.hiddens', []);
        $sessionMasked = (array) $config->get('debugbar.options.session.masked', []);

        // Legacy hidden values, using array path
        $hiddens = array_map(function ($value) {
            if (str_contains($value, '.')) {
                return substr($value, strrpos($value, '.') + 1);
            }
            return $value;
        }, array_merge((array) ($options['hiddens'] ?? []), $sessionHiddens));

        $masked = array_merge((array) ($options['masked'] ?? []), $sessionMasked);

        $requestCollector = new RequestCollector($request);
        $requestCollector->addMaskedKeys($hiddens);
        $requestCollector->addMaskedKeys($masked);
        $requestCollector->setCurrentRequestId($this->debugbar->getCurrentRequestId());

        $this->addCollector($requestCollector);

        $events->listen(ResponsePrepared::class, fn(ResponsePrepared $e) => $requestCollector->setResponse($e->response));
    }
}
