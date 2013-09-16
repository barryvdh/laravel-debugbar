<?php
namespace Barryvdh\Debugbar\Console;
use Illuminate\Foundation\AssetPublisher;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
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

        $package = 'barryvdh/laravel-debugbar';
        if ( ! is_null($path = $this->getPath()))
        {
            $this->assets->publish($package, $path);
            $this->info('Assets published for package: '.$package);
        }
        else
        {
            $this->error('Could not find path for: '.$package);
        }

    }

    protected function getPath(){
        $reflector = new \ReflectionClass('DebugBar\DebugBar');
        return dirname($reflector->getFileName()) . DIRECTORY_SEPARATOR . 'Resources';
    }


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }



}
