<?php namespace Barryvdh\Debugbar;


class ServiceProvider extends \Illuminate\Support\ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if($this->app['config']->get('laravel-debugbar::config.enabled')){
            /** @var LaravelDebugbar $debugbar */
            $debugbar = $this->app['debugbar'];
            $debugbar->boot();
        }
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->package('barryvdh/laravel-debugbar');

        $self = $this;
        $this->app['debugbar'] = $this->app->share(function ($app) use($self) {
                $debugbar = new LaravelDebugBar($app);

                $sessionManager = $app['session'];
                $httpDriver = new SymfonyHttpDriver($sessionManager);
                $debugbar->setHttpDriver($httpDriver);

                return $debugbar;
            });

        $this->app['command.debugbar.publish'] = $this->app->share(function($app)
            {
                return new Console\PublishCommand($app['asset.publisher']);
            });
        $this->commands('command.debugbar.publish');


    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('debugbar', 'command.debugbar.publish');
    }





}
