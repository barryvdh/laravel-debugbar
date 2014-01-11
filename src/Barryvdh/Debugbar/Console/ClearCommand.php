<?php namespace Barryvdh\Debugbar\Console;

use DebugBar\DebugBar;
use Illuminate\Console\Command;

class ClearCommand extends Command {

    protected $name = 'debugbar:clear';
    protected $description = 'Clear the Debugbar Storage';
    protected $debugbar;


    public function __construct(DebugBar $debugbar){
        $this->debugbar = $debugbar;

        parent::__construct();
    }

    public function fire(){

        $storage = $this->debugbar->getStorage();
        $storage->clear();
        $this->info('Debugbar Storage cleared!');

    }
}