<?php

namespace Barryvdh\Debugbar\Console;

use Barryvdh\Debugbar\LaravelDebugbar;
use DebugBar\DebugBar;
use Illuminate\Console\Command;

class ClearCommand extends Command
{
    protected $name = 'debugbar:clear';
    protected $description = 'Clear the Debugbar Storage';


    public function handle(LaravelDebugbar $debugbar)
    {
        $debugbar->boot();

        if ($storage = $debugbar->getStorage()) {
            try {
                $storage->clear();
            } catch (\InvalidArgumentException $e) {
                // hide InvalidArgumentException if storage location does not exist
                if (strpos($e->getMessage(), 'does not exist') === false) {
                    throw $e;
                }
            }
            $this->info('Debugbar Storage cleared!');
        } else {
            $this->error('No Debugbar Storage found..');
        }
    }
}
