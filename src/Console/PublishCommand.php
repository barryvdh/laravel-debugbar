<?php
namespace Barryvdh\Debugbar\Console;

use Illuminate\Console\Command;

/**
 * Publish the Debugbar assets to the public directory
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @deprecated No longer needed because of the AssetController
 */
class PublishCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'debugbar:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the Debugbar assets';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->info(
            'NOTICE: Since laravel-debugbar 1.7.x, publishing assets is no longer necessary. The assets in public/packages/barryvdh/laravel-debugbar and maximebf/php-debugbar can be safely removed.'
        );
    }
}
