<?php namespace Barryvdh\Debugbar\Console;

use DebugBar\DebugBar;
use Illuminate\Console\Command;

class ClearCommand extends Command
{
    protected $name = 'debugbar:clear';
    protected $description = 'Clear the Debugbar Storage';
    protected $debugbar;

    public function __construct(DebugBar $debugbar)
    {
        $this->debugbar = $debugbar;

        parent::__construct();
    }

    public function fire()
    {
        $this->debugbar->boot();

        if ($storage = $this->debugbar->getStorage()) {
            try
            {
                $storage->clear();
            } catch(\InvalidArgumentException $e) {
                // hide InvalidArgumentException if storage location does not exist
                if(strpos($e->getMessage(), 'does not exist') === false) {
                    throw $e;
                }
            }
            $this->info('Debugbar Storage cleared!');
        } else {
            $this->error('No Debugbar Storage found..');
        }
    }
}
