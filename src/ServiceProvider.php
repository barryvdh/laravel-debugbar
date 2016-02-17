<?php namespace Barryvdh\Debugbar;

use Illuminate\Routing\Router;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/debugbar.php';
        $this->mergeConfigFrom($configPath, 'debugbar');
        
        $this->app->alias(
            'DebugBar\DataFormatter\DataFormatter',
            'DebugBar\DataFormatter\DataFormatterInterface'
        );
        
        $this->app->singleton('debugbar', function ($app) {
                $debugbar = new LaravelDebugbar($app);

                $sessionManager = $app['session'];
                $httpDriver = new SymfonyHttpDriver($sessionManager);
                $debugbar->setHttpDriver($httpDriver);

                return $debugbar;
            }
        );
        
        $this->app->alias('debugbar', 'Barryvdh\Debugbar\LaravelDebugbar');

        $this->app['command.debugbar.clear'] = $this->app->share(
            function ($app) {
                return new Console\ClearCommand($app['debugbar']);
            }
        );

        $this->commands(array('command.debugbar.clear'));
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $app = $this->app;

        $configPath = __DIR__ . '/../config/debugbar.php';
        $this->publishes([$configPath => $this->getConfigPath()], 'config');

        if ($app->runningInConsole()) {
            return;
        }

        $routeConfig = [
            'namespace' => 'Barryvdh\Debugbar\Controllers',
            'prefix' => $this->app['config']->get('debugbar.route_prefix'),
        ];

        $this->getRouter()->group($routeConfig, function($router) {
            $router->get('open', [
                'uses' => 'OpenHandlerController@handle',
                'as' => 'debugbar.openhandler',
            ]);

            $router->get('clockwork/{id}', [
                'uses' => 'OpenHandlerController@clockwork',
                'as' => 'debugbar.clockwork',
            ]);

            $router->get('assets/stylesheets', [
                'uses' => 'AssetController@css',
                'as' => 'debugbar.assets.css',
            ]);

            $router->get('assets/javascript', [
                'uses' => 'AssetController@js',
                'as' => 'debugbar.assets.js',
            ]);
        });

        $enabled = $this->app['config']->get('debugbar.enabled');

        // If enabled is null, set from the app.debug value
        if (is_null($enabled)) {
            $enabled = $this->checkAppDebug();
            $this->app['config']->set('debugbar.enabled', $enabled);
        }

        if ( ! $enabled) {
            return;
        }

        /** @var LaravelDebugbar $debugbar */
        $debugbar = $this->app['debugbar'];
        $debugbar->boot();

        $this->registerMiddleware('Barryvdh\Debugbar\Middleware\Debugbar');
    }

    /**
     * Get the active router.
     *
     * @return Router
     */
    protected function getRouter()
    {
        return $this->app['router'];
    }

    /**
     * Get the config path
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return config_path('debugbar.php');
    }

    /**
     * Publish the config file
     *
     * @param  string $configPath
     */
    protected function publishConfig($configPath)
    {
        $this->publishes([$configPath => config_path('debugbar.php')], 'config');
    }

    /**
     * Register the Debugbar Middleware
     *
     * @param  string $middleware
     */
    protected function registerMiddleware($middleware)
    {
        $kernel = $this->app['Illuminate\Contracts\Http\Kernel'];
        $kernel->pushMiddleware($middleware);
    }

    /**
     * Check the App Debug status
     */
    protected function checkAppDebug()
    {
        return $this->app['config']->get('app.debug');
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
