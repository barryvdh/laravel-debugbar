<?php

namespace Barryvdh\Debugbar\Tests\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendNotification implements ShouldQueue
{
    use Dispatchable;

    public function handle()
    {
        // Do Nothing
    }
}
