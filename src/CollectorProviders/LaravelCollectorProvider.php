<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\CollectorProviders;

use Barryvdh\Debugbar\DataCollector\LaravelCollector;
use Illuminate\Contracts\Foundation\Application;

class LaravelCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Application $app, array $options): void
    {
        $this->addCollector(new LaravelCollector($app));
    }
}
