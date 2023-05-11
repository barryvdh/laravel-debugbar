<?php

namespace Barryvdh\Debugbar\Commands;

use DebugBar\DebugBar;
use Illuminate\Console\Command;

class ClearCommand extends Command
{
    protected $name = 'debugbar:clear';

    protected $description = 'Clear the Debugbar Storage';

    public function __construct(protected DebugBar $debugbar)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->debugbar->boot();

        $storage = $this->debugbar->getStorage();

        if (! $storage) {
            $this->components->error('No Debugbar Storage found.');

            return self::FAILURE;
        }

        try {
            $storage->clear();
        } catch (\InvalidArgumentException $e) {
            // hide InvalidArgumentException if storage location does not exist
            if (! str_contains($e->getMessage(), 'does not exist')) {
                throw $e;
            }
        }

        $this->components->info('Debugbar Storage cleared!');

        return self::SUCCESS;
    }
}
