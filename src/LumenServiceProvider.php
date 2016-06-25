<?php namespace Barryvdh\Debugbar;

use Laravel\Lumen\Application;

class LumenServiceProvider extends ServiceProvider
{
    /** @var  Application */
    protected $app;

    /**
     * Get the active router.
     *
     * @return Application
     */
    protected function getRouter()
    {
        return $this->app;
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
     * Check the App Debug status
     */
    protected function checkAppDebug()
    {
        return env('APP_DEBUG');
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
