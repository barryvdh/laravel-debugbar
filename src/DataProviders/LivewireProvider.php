<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataProviders;

use Barryvdh\Debugbar\DataCollector\LivewireCollector;
use Illuminate\Contracts\Foundation\Application;

class LivewireProvider extends AbstractDataProvider
{
    public function __invoke(Application $app): void
    {
        if ($app->bound('livewire')) {
            $this->addCollector($app->make(LivewireCollector::class));
        }
    }
}
