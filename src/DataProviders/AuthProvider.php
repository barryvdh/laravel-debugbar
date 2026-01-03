<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataProviders;

use Barryvdh\Debugbar\DataCollector\MultiAuthCollector;
use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\PreparingResponse;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Events\Routing;
use Illuminate\Support\Facades\Config;

class AuthProvider extends AbstractDataProvider
{
    public function __invoke(Config $appConfig, AuthManager $auth, array $config): void
    {
        $guards = $appConfig->get('auth.guards', []);
        $authCollector = new MultiAuthCollector($auth, $guards);
        $this->addCollector($authCollector);

        $authCollector->setShowName($config['show_name'] ?? false);
        $authCollector->setShowGuardsData($config['show_guards'] ?? true);
    }
}
