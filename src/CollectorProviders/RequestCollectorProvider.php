<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use Barryvdh\Debugbar\DataCollector\RequestCollector;
use Illuminate\Config\Repository;
use Illuminate\Http\Request;

class RequestCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Repository $config, Request $request, array $options): void
    {
        $sessionHiddens = $config->get('debugbar.options.session.hiddens', []);
        $sessionMasked = $config->get('debugbar.options.session.masked', []);

        // Legacy hidden values, using array path
        $hiddens = array_map(function ($value) {
            if (str_contains($value, '.')) {
                return substr($value, strrpos($value, '.') + 1);
            }
            return $value;
        }, array_merge($options['hiddens'] ?? [], $sessionHiddens));

        $masked = array_merge($options['masked'] ?? [], $sessionMasked);

        $requestCollector = new RequestCollector($request);
        $requestCollector->addMaskedKeys($hiddens);
        $requestCollector->addMaskedKeys($masked);
        $requestCollector->setCurrentRequestId($this->debugbar->getCurrentRequestId());

        $this->addCollector($requestCollector);
    }
}
