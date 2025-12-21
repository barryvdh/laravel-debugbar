<?php

namespace Barryvdh\Debugbar;

use Laravel\Lumen\Application;
use DebugBar\DataCollector\TimeDataCollector;

class LumenServiceProvider extends ServiceProvider
{
    /** @var  Application */
    protected $app;

    public function boot()
    {
        parent::boot();

        $this->app->call(
            function () {
                $debugBar = $this->app->get(LaravelDebugbar::class);
                if ($debugBar->shouldCollect('time', true)) {
                    $startTime = $this->app['request']->server('REQUEST_TIME_FLOAT');

                    if (!$debugBar->hasCollector('time')) {
                        $debugBar->addCollector(new TimeDataCollector($startTime));
                    }

                    if ($this->app['config']->get('debugbar.options.time.memory_usage')) {
                        $debugBar['time']->showMemoryUsage();
                    }

                    if ($startTime) {
                        $debugBar->addMeasure('Booting', $startTime, microtime(true), [], 'time');
                    }
                }
            }
        );
    }

    /**
     * Get the active router.
     *
     * @return Application
     */
    protected function getRouter()
    {
        return $this->app->router;
    }

    /**
     * Get the config path
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return base_path('config/debugbar.php');
    }

    /**
     * Register the Debugbar Middleware
     *
     * @param  string $middleware
     */
    protected function registerMiddleware($middleware)
    {
        $this->app->middleware([$middleware]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['debugbar', 'command.debugbar.clear'];
    }
}
