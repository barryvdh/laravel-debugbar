<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataProviders;

use Barryvdh\Debugbar\DataCollector\LaravelCollector;
use Illuminate\Contracts\Foundation\Application;

class LaravelProvider extends AbstractDataProvider
{
    public function __invoke(Application $app, array $config): void
    {
        $this->addCollector(new LaravelCollector($app));
    }
}
