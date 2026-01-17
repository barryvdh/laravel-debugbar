<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar;

use DebugBar\Bridge\Symfony\SymfonyHttpDriver;
use DebugBar\DataFormatter\DataFormatter;
use DebugBar\DataFormatter\DataFormatterInterface;
use Fruitcake\LaravelDebugbar\Middleware\InjectDebugbar;
use Fruitcake\LaravelDebugbar\Support\Octane\ResetDebugbar;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Session\SymfonySessionDecorator;
use Illuminate\Support\Collection;
use Laravel\Octane\Events\RequestReceived;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register the service provider.
     *
     */
    public function register(): void
    {
        $configPath = __DIR__ . '/../config/debugbar.php';
        $this->mergeConfigFrom($configPath, 'debugbar');

        $this->app->alias(
            DataFormatter::class,
            DataFormatterInterface::class,
        );

        $this->app->singleton(LaravelDebugbar::class);
        $this->app->alias(LaravelDebugbar::class, 'debugbar');

        $this->app->singleton(SymfonyHttpDriver::class, function ($app): \DebugBar\Bridge\Symfony\SymfonyHttpDriver {
            return new SymfonyHttpDriver($app->make(SymfonySessionDecorator::class));
        });

        if ($this->app->runningInConsole()) {
            $this->app->bind(
                'command.debugbar.clear',
                function ($app): \Fruitcake\LaravelDebugbar\Console\ClearCommand {
                    return new Console\ClearCommand($app['debugbar']);
                },
            );
        }

        Collection::macro('debug', function (): \Illuminate\Support\Collection {
            debug($this);
            return $this;
        });
    }

    /**
     * Bootstrap the application events.
     *
     */
    public function boot(Dispatcher $events): void
    {
        if ($this->app->runningInConsole()) {
            $configPath = __DIR__ . '/../config/debugbar.php';
            $this->publishes([$configPath => $this->getConfigPath()], 'config');

            $this->commands(['command.debugbar.clear']);
        }

        $this->loadRoutesFrom(__DIR__ . '/debugbar-routes.php');

        $this->registerMiddleware(InjectDebugbar::class);

        // Reset the debugbar instance on each new Octane request
        $events->listen(RequestReceived::class, ResetDebugbar::class);

        // Resolve the LaravelDebugbar instance during boot to force it to be loaded in the Octane sandbox
        $debugbar = $this->app->make(LaravelDebugbar::class);

        // Register boot time
        $this->booted(fn() => $debugbar->booted());

        // Fallback for when Middleware is never run, but this cannot write anything to the session
        $events->listen(RequestHandled::class, function (RequestHandled $event) use ($debugbar): void {
            if ($debugbar->isEnabled() && !$debugbar->requestIsExcluded()) {
                $debugbar->modifyResponse($event->request, $event->response);
            }
        });
    }

    /**
     * Get the config path
     *
     */
    protected function getConfigPath(): string
    {
        return config_path('debugbar.php');
    }

    /**
     * Register the Debugbar Middleware
     *
     */
    protected function registerMiddleware(string $middleware): void
    {
        /** @var \Illuminate\Foundation\Http\Kernel $kernel */
        $kernel = $this->app[Kernel::class];
        $kernel->pushMiddleware($middleware);
        if (isset($kernel->getMiddlewareGroups()['web'])) {
            $kernel->appendMiddlewareToGroup('web', $middleware);
        }
    }
}
