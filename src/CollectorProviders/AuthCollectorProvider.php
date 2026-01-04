<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use Barryvdh\Debugbar\DataCollector\MultiAuthCollector;
use Illuminate\Auth\AuthManager;
use Illuminate\Config\Repository;

class AuthCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Repository $config, AuthManager $auth, array $options): void
    {
        $guards = $config->get('auth.guards', []);
        $authCollector = new MultiAuthCollector($auth, $guards);
        $this->addCollector($authCollector);

        $authCollector->setShowName($options['show_name'] ?? false);
        $authCollector->setShowGuardsData($options['show_guards'] ?? true);
    }
}
