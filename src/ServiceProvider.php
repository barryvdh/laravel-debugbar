<?php

namespace Barryvdh\Debugbar;

use Barryvdh\Debugbar\Middleware\DebugbarEnabled;
use Barryvdh\Debugbar\Middleware\InjectDebugbar;
use DebugBar\DataFormatter\DataFormatter;
use DebugBar\DataFormatter\DataFormatterInterface;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Collection;
use Illuminate\View\Engines\EngineResolver;
use Barryvdh\Debugbar\Facade as DebugBar;

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
            $debugbar = new LaravelDebugbar($app);

            if ($app->bound(SessionManager::class)) {
                $sessionManager = $app->make(SessionManager::class);
                $httpDriver = new SymfonyHttpDriver($sessionManager);
                $debugbar->setHttpDriver($httpDriver);
            }

            return $debugbar;
        });

        $this->app->alias(LaravelDebugbar::class, 'debugbar');

        $this->app->singleton(
            'command.debugbar.clear',
            function ($app) {
                return new Console\ClearCommand($app['debugbar']);
            }
        );

        $this->app->extend(
            'view.engine.resolver',
            function (EngineResolver $resolver, Application $application): EngineResolver {
                $laravelDebugbar = $application->make(LaravelDebugbar::class);

                $shouldTrackViewTime = $laravelDebugbar->isEnabled() &&
                    $laravelDebugbar->shouldCollect('time', true) &&
                    $laravelDebugbar->shouldCollect('views', true) &&
                    $application['config']->get('debugbar.options.views.timeline', false);

                if (! $shouldTrackViewTime) {
                    /* Do not swap the engine to save performance */
                    return $resolver;
                }

                return new class ($resolver, $laravelDebugbar) extends EngineResolver {
                    private $laravelDebugbar;

                    public function __construct(EngineResolver $resolver, LaravelDebugbar $laravelDebugbar)
                    {
                        foreach ($resolver->resolvers as $engine => $resolver) {
                            $this->register($engine, $resolver);
                        }
                        $this->laravelDebugbar = $laravelDebugbar;
                    }

                    public function register($engine, \Closure $resolver)
                    {
                        parent::register($engine, function () use ($resolver) {
                            return new DebugbarViewEngine($resolver(), $this->laravelDebugbar);
                        });
                    }
                };
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

        $this->loadRoutesFrom(realpath(__DIR__ . '/debugbar-routes.php'));

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
}
