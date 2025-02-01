<?php

namespace Barryvdh\Debugbar;

use Barryvdh\Debugbar\Middleware\InjectDebugbar;
use DebugBar\DataFormatter\DataFormatter;
use DebugBar\DataFormatter\DataFormatterInterface;
use Illuminate\Container\Container;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Events\ResponsePrepared;
use Illuminate\Routing\Router;
use Illuminate\Session\CookieSessionHandler;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Collection;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
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

        $this->app->singleton(LaravelDebugbar::class, function ($app) {
            return new LaravelDebugbar($app);
        });

        $this->app->singleton(SymfonyHttpDriver::class, function ($app) {
            return new SymfonyHttpDriver($app->make(SessionManager::class));
        });

        $this->app->alias(LaravelDebugbar::class, 'debugbar');

        $this->app->singleton(
            'command.debugbar.clear',
            function ($app) {
                return new Console\ClearCommand($app['debugbar']);
            }
        );

        Collection::macro('debug', function () {
            debug($this);
            return $this;
        });
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

        $this->loadRoutesFrom(__DIR__ . '/debugbar-routes.php');

        $this->registerMiddleware(InjectDebugbar::class);

        $this->commands(['command.debugbar.clear']);
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
     * Register the Debugbar Middleware
     *
     * @param  string $middleware
     */
    protected function registerMiddleware($middleware)
    {
        /** @var \Illuminate\Foundation\Http\Kernel $kernel */
        $kernel = $this->app[Kernel::class];
        $kernel->pushMiddleware($middleware);
        if (isset($kernel->getMiddlewareGroups()['web'])) {
            $kernel->appendMiddlewareToGroup('web', $middleware);
        }
    }
}
