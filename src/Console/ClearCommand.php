<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Console;

use Fruitcake\LaravelDebugbar\LaravelDebugbar;
use Illuminate\Console\Command;

class ClearCommand extends Command
{
    protected $name = 'debugbar:clear';
    protected $description = 'Clear the Debugbar Storage';

    public function handle(LaravelDebugbar $debugbar): void
    {
        $debugbar->boot();

        if ($storage = $debugbar->getStorage()) {
            try {
                $storage->clear();
            } catch (\InvalidArgumentException $e) {
                // hide InvalidArgumentException if storage location does not exist
                if (!str_contains($e->getMessage(), 'does not exist')) {
                    throw $e;
                }
            }
            $this->info('Debugbar Storage cleared!');
        } else {
            $this->error('No Debugbar Storage found..');
        }
    }
}
