<?php

namespace Barryvdh\Debugbar\Tests\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderShipped implements ShouldQueue
{
    use Dispatchable;

    private $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle()
    {
        // Do Nothing
    }
}
