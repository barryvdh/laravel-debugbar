<?php namespace Barryvdh\Debugbar;

use Barryvdh\Debugbar\Middleware\DebugbarEnabled;
use Barryvdh\Debugbar\Middleware\InjectDebugbar;
use DebugBar\DataFormatter\DataFormatter;
use DebugBar\DataFormatter\DataFormatterInterface;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Session\SessionManager;

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
            DataFormatter::class,
            DataFormatterInterface::class
        );

        $this->app->singleton(LaravelDebugbar::class, function () {
                $debugbar = new LaravelDebugbar($this->app);

                if ($this->app->bound(SessionManager::class)) {
                    $sessionManager = $this->app->make(SessionManager::class);
                    $httpDriver = new SymfonyHttpDriver($sessionManager);
                    $debugbar->setHttpDriver($httpDriver);
                }

                return $debugbar;
            }
        );

        $this->app->alias(LaravelDebugbar::class, 'debugbar');

        $this->app->singleton('command.debugbar.clear',
            function ($app) {
                return new Console\ClearCommand($app['debugbar']);
            }
        );

        $this->commands(['command.debugbar.clear']);
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/debugbar.php';
        $this->publishes([$configPath => $this->getConfigPath()], 'config');

        $routeConfig = [
            'namespace' => 'Barryvdh\Debugbar\Controllers',
            'prefix' => $this->app['config']->get('debugbar.route_prefix'),
            'domain' => $this->app['config']->get('debugbar.route_domain'),
            'middleware' => [DebugbarEnabled::class],
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

            $router->get('telescope/{id}', [
                'uses' => 'TelescopeController@show',
                'as' => 'debugbar.telescope',
            ]);
            
            $router->get('assets/stylesheets', [
                'uses' => 'AssetController@css',
                'as' => 'debugbar.assets.css',
            ]);

            $router->get('assets/javascript', [
                'uses' => 'AssetController@js',
                'as' => 'debugbar.assets.js',
            ]);

            $router->delete('cache/{key}/{tags?}', [
                'uses' => 'CacheController@delete',
                'as' => 'debugbar.cache.delete',
            ]);
        });

        $this->registerMiddleware(InjectDebugbar::class);
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
        $kernel = $this->app[Kernel::class];
        $kernel->pushMiddleware($middleware);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['debugbar', 'command.debugbar.clear', DataFormatterInterface::class, LaravelDebugbar::class];
    }
}
