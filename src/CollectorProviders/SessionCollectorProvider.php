<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use Barryvdh\Debugbar\DataCollector\MultiAuthCollector;
use Barryvdh\Debugbar\DataCollector\SessionCollector;
use DebugBar\DataCollector\ConfigCollector;
use Illuminate\Auth\AuthManager;
use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

class SessionCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Request $request, array $options): void
    {
    }
}
