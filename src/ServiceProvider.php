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

        $this->app->singleton(LaravelDebugbar::class, function ($app): LaravelDebugbar {
            return new LaravelDebugbar($app);
        });
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
        } else {
            $this->booted(function(): void {
                $startTime = defined('LARAVEL_START') ? LARAVEL_START : $this->app['request']->server('REQUEST_TIME_FLOAT');
                if ($startTime) {
                    $this->app->make(LaravelDebugbar::class)->addMeasure('Booting', (float) $startTime);
                }
            });
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
        $this->loadRoutesFrom(__DIR__ . '/debugbar-routes.php');

        $this->registerMiddleware(InjectDebugbar::class);

        $this->commands(['command.debugbar.clear']);

        // Reset the debugbar instance on each new Octane request
        $events->listen(RequestReceived::class, ResetDebugbar::class);

        if ($this->app->runningInConsole()) {
            $configPath = __DIR__ . '/../config/debugbar.php';
            $this->publishes([$configPath => $this->getConfigPath()], 'config');
        } else {
            // Resolve the LaravelDebugbar instance during boot to force it to be loaded in the Octane sandbox
            $this->app->make(LaravelDebugbar::class);
        }
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
