<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use Barryvdh\Debugbar\DataCollector\LivewireCollector;
use Illuminate\Contracts\Foundation\Application;

class LivewireCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Application $app): void
    {
        if ($app->bound('livewire')) {
            $this->addCollector($app->make(LivewireCollector::class));
        }
    }
}
