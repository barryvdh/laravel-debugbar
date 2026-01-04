<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use Barryvdh\Debugbar\DataCollector\MultiAuthCollector;
use Barryvdh\Debugbar\DataCollector\RequestCollector;
use Barryvdh\Debugbar\DataCollector\SessionCollector;
use DebugBar\DataCollector\ConfigCollector;
use Illuminate\Auth\AuthManager;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Session\SessionManager;

class RequestCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Application $app, Repository $config, array $options): void
    {
        $sessionHiddens = $config->get('debugbar.options.session_hiddens', []);
        $requestHiddens = array_merge(
            $options['hiddens'] ?? [],
            array_map(fn($key) => 'session_attributes.' . $key, $sessionHiddens),
        );
        if (!$this->hasCollector('request')) {

                $reqId = $this->debugbar->getCurrentRequestId();
                $this->addCollector(new RequestCollector($request, $response, $sessionManager, $reqId, $requestHiddens));

        }
    }
}
