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

        $app['config']->package('barryvdh/laravel-debugbar', __DIR__ . '/config');

        if ($app->runningInConsole()) {
            if ($this->app['config']->get('laravel-debugbar::config.capture_console') && method_exists($app, 'shutdown')) {
                $app->shutdown(
                    function ($app) {
                        /** @var LaravelDebugbar $debugbar */
                        $debugbar = $app['debugbar'];
                        $debugbar->collectConsole();
                    }
                );
            } else {
                $this->app['config']->set('laravel-debugbar::config.enabled', false);
            }
        } elseif (!$this->shouldUseMiddleware()) {
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

        if ($this->app['config']->get('laravel-debugbar::config.enabled')) {
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
        $this->app->alias(
            'DebugBar\DataFormatter\DataFormatter',
            'DebugBar\DataFormatter\DataFormatterInterface'
        );
        
        $this->app['debugbar'] = $this->app->share(
            function ($app) {
                $debugbar = new LaravelDebugbar($app);

                $sessionManager = $app['session'];
                $httpDriver = new SymfonyHttpDriver($sessionManager);
                $debugbar->setHttpDriver($httpDriver);

                return $debugbar;
            }
        );

        $this->app['command.debugbar.publish'] = $this->app->share(
            function ($app) {
                return new Console\PublishCommand();
            }
        );

        $this->app['command.debugbar.clear'] = $this->app->share(
            function ($app) {
                return new Console\ClearCommand($app['debugbar']);
            }
        );

        $this->commands(array('command.debugbar.publish', 'command.debugbar.clear'));

        if ($this->shouldUseMiddleware()) {
            $this->app->middleware('Barryvdh\Debugbar\Middleware\Stack', array($this->app));
        }
    }

    /**
     * Detect if the Middelware should be used.
     * 
     * @return bool
     */
    protected function shouldUseMiddleware()
    {
        $app = $this->app;
        $version = $app::VERSION;
        return !$app->runningInConsole() && version_compare($version, '4.1-dev', '>=') && version_compare($version, '5.0-dev', '<');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('debugbar', 'command.debugbar.publish', 'command.debugbar.clear');
    }
}
