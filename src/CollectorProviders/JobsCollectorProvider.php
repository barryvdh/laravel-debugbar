<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\CollectorProviders;

use DebugBar\DataCollector\ObjectCountCollector;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\Events\JobQueued;

class JobsCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Dispatcher $events, array $options): void
    {
        $jobs = new ObjectCountCollector('jobs', 'briefcase');
        $this->addCollector($jobs);

        $events->listen(JobQueued::class, function ($event) use ($jobs) {
            $jobs->countClass($event->job);
        });
    }
}
