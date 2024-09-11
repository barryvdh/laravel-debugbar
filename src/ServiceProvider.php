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

        $this->app->singleton(SessionHttpDriver::class, function($app) {
            // Attach the Cookie Handler with Response
            $cookieHandler = new CookieSessionHandler($app->make('cookie'), 0, true);
            $cookieHandler->setRequest($app['request']);
            return new SessionHttpDriver($cookieHandler);
        });

        $this->app->singleton(SymfonyHttpDriver::class, function($app) {
            return new SymfonyHttpDriver($app->make(SessionManager::class));
        });

        $this->app->alias(LaravelDebugbar::class, 'debugbar');

        $this->app->singleton(
            'command.debugbar.clear',
            function ($app) {
                return new Console\ClearCommand($app['debugbar']);
            }
        );

        $this->app->extend(
            'view',
            function (Factory $factory, Container $application): Factory {
                $laravelDebugbar = $application->make(LaravelDebugbar::class);

                $shouldTrackViewTime = $laravelDebugbar->isEnabled() &&
                    $laravelDebugbar->shouldCollect('time', true) &&
                    $laravelDebugbar->shouldCollect('views', true) &&
                    $application['config']->get('debugbar.options.views.timeline', false);

                if (! $shouldTrackViewTime) {
                    /* Do not swap the engine to save performance */
                    return $factory;
                }

                $extensions = array_reverse($factory->getExtensions());
                $engines = array_flip($extensions);
                $enginesResolver = $application->make('view.engine.resolver');

                foreach ($engines as $engine => $extension) {
                    $resolved = $enginesResolver->resolve($engine);

                    $factory->addExtension($extension, $engine, function () use ($resolved, $laravelDebugbar) {
                        return new DebugbarViewEngine($resolved, $laravelDebugbar);
                    });
                }

                // returns original order of extensions
                foreach ($extensions as $extension => $engine) {
                    $factory->addExtension($extension, $engine);
                }

                return $factory;
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

        $this->registerResponseListener();
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

    /**
     * Register the Response Listener
     *
     * @param  string $middleware
     */
    protected function registerResponseListener()
    {
        if (!isset($this->app['events']) || !class_exists(ResponsePrepared::class)) {
            return;
        }

        /**
         * For redirects, prepare the response early to store in the session.
         * For regular requests, get the stacked data early
         */
        $this->app['events']->listen(ResponsePrepared::class, function (ResponsePrepared $event) {
            /** @var LaravelDebugbar $debugbar */
            $debugbar = $this->app->make(LaravelDebugbar::class);
            if ($debugbar->isEnabled()) {

                $httpDriver = $debugbar->getHttpDriver();
                if ($httpDriver instanceof SessionHttpDriver || $httpDriver instanceof SymfonyHttpDriver) {
                    $httpDriver->setResponse($event->response);
                }

                if ($event->response->isRedirection()) {
                    $debugbar->modifyResponse($event->request, $event->response);
                } else {
                    $debugbar->getStackedData();
                }
            }
        });
    }
}
