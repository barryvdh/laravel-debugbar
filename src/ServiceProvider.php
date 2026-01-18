<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar;

use DebugBar\DataFormatter\DataFormatter;
use DebugBar\DataFormatter\DataFormatterInterface;
use Fruitcake\LaravelDebugbar\Support\Octane\ResetDebugbar;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Http\Events\RequestHandled;
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
        $this->app->singleton(LaravelHttpDriver::class);

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

        // Reset the debugbar instance on each new Octane request
        $events->listen(RequestReceived::class, ResetDebugbar::class);

        // Resolve the LaravelDebugbar instance during boot to force it to be loaded in the Octane sandbox
        $debugbar = $this->app->make(LaravelDebugbar::class);

        $events->listen(RequestHandled::class, function ($event) use ($debugbar): void {
            $debugbar->setResponse($event->response);
            $debugbar->modifyResponse($event->response);
        });

        if (! $this->app->runningInConsole()) {

            // Exclude debugbar cookies from encryption
            EncryptCookies::except($debugbar->getStackDataSessionNamespace());

            // Attach listeners
            if ($debugbar->isEnabled() && !$debugbar->requestIsExcluded()) {
                $debugbar->boot();
            }

            // Register boot time, regardless of already being booted
            $this->booted(fn() => $debugbar->booted());
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
}
