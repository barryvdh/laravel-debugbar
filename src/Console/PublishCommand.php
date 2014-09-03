<?php
namespace Barryvdh\Debugbar\Console;

use Illuminate\Console\Command;

/**
 * Publish the Debugbar assets to the public directory
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @deprecated No longer needed because of the AssetController
 */
class PublishCommand extends Command {

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
     * The asset publisher instance.
     *
     * @var \Illuminate\Foundation\AssetPublisher
     */
    protected $assetPublisher;

    /**
     * Create a new Publish command
     *
     * @param \Illuminate\Foundation\AssetPublisher $assetPublisher
     */
    public function __construct($assetPublisher)
    {
        parent::__construct();

        $this->assetPublisher = $assetPublisher;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {

        $package = 'maximebf/php-debugbar';
        if ( ! is_null($path = $this->getDebugBarPath()))
        {
            $this->assetPublisher->publish($package, $path);
            $this->info('Assets published for package: '.$package);
        }
        else
        {
            $this->error('Could not find path for: '.$package);
        }
        $this->assetPublisher->publish('barryvdh/laravel-debugbar', $this->getPackagePublicPath());
        $this->info('Assets published for package: barryvdh/laravel-debugbar');

    }

    protected function getDebugBarPath() {
        $reflector = new \ReflectionClass('DebugBar\DebugBar');
        return dirname($reflector->getFileName()) . DIRECTORY_SEPARATOR . 'Resources';
    }

    protected function getPackagePublicPath() {
        return __DIR__.'/../Resources';
    }

}
