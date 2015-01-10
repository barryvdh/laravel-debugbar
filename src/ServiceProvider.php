<?php namespace Barryvdh\Debugbar;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $app = $this->app;

        if (!$app->runningInConsole()) {
            $app['router']->after(
                function ($request, $response) use ($app) {
                    /** @var LaravelDebugbar $debugbar */
                    $debugbar = $app['debugbar'];
                    $debugbar->modifyResponse($request, $response);
                }
            );
        }

        $this->app['router']->get(
            '_debugbar/open',
            array(
                'uses' => 'Barryvdh\Debugbar\Controllers\OpenHandlerController@handle',
                'as' => 'debugbar.openhandler',
            )
        );

        $this->app['router']->get(
            '_debugbar/assets/stylesheets',
            array(
                'uses' => 'Barryvdh\Debugbar\Controllers\AssetController@css',
                'as' => 'debugbar.assets.css',
            )
        );

        $this->app['router']->get(
            '_debugbar/assets/javascript',
            array(
                'uses' => 'Barryvdh\Debugbar\Controllers\AssetController@js',
                'as' => 'debugbar.assets.js',
            )
        );

        if ($this->app['config']->get('debugbar.enabled')) {
            /** @var LaravelDebugbar $debugbar */
            $debugbar = $this->app['debugbar'];
            $debugbar->boot();
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['config']->set('debugbar', require __DIR__ .'/../config/config.php');
        
        $this->app->alias(
            'DebugBar\DataFormatter\DataFormatter',
            'DebugBar\DataFormatter\DataFormatterInterface'
        );
        
        $this->app['debugbar'] = $this->app->share(
            function ($app) {
                $debugbar = new LaravelDebugBar($app);

                $sessionManager = $app['session'];
                $httpDriver = new SymfonyHttpDriver($sessionManager);
                $debugbar->setHttpDriver($httpDriver);

                return $debugbar;
            }
        );

        $this->app['command.debugbar.clear'] = $this->app->share(
            function ($app) {
                return new Console\ClearCommand($app['debugbar']);
            }
        );

        $this->commands(array('command.debugbar.publish', 'command.debugbar.clear'));
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('debugbar', 'command.debugbar.clear');
    }
}
