<?php
namespace Barryvdh\Debugbar\Console;
use Illuminate\Foundation\AssetPublisher;
use Illuminate\Console\Command;

/**
 * Publish the Debugbar assets to the public directory
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
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
    protected $assets;


    /**
     * Create a new Publish command
     *
     * @param \Illuminate\Foundation\AssetPublisher $assets
     */
    public function __construct(AssetPublisher $assets)
    {
        parent::__construct();

        $this->assets = $assets;
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
            $this->assets->publish($package, $path);
            $this->info('Assets published for package: '.$package);
        }
        else
        {
            $this->error('Could not find path for: '.$package);
        }
        $this->assets->publish('barryvdh/laravel-debugbar', $this->getPackagePublicPath());
        $this->info('Assets published for package: barryvdh/laravel-debugbar');

    }

    protected function getDebugBarPath(){
        $reflector = new \ReflectionClass('DebugBar\DebugBar');
        return dirname($reflector->getFileName()) . DIRECTORY_SEPARATOR . 'Resources';
    }

    protected function getPackagePublicPath(){
        return __DIR__.'/../../../../public';
    }


}
