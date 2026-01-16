<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\CollectorProviders;

use Fruitcake\LaravelDebugbar\DataCollector\CacheCollector;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;

class CacheCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Request $request, Dispatcher $events, array $options): void
    {
        $collectValues = $options['values'] ?? false;
        $startTime = (float) $request->server('REQUEST_TIME_FLOAT');
        $cacheCollector = new CacheCollector($startTime, $collectValues);
        $this->addCollector($cacheCollector);
        $cacheCollector->subscribe($events);
    }
}
