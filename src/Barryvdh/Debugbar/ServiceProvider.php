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
        $app = $this->app;
        $app['config']->package('barryvdh/laravel-debugbar', $this->guessPackagePath() . '/config');

        if($app->runningInConsole()){
            if($this->app['config']->get('laravel-debugbar::config.capture_console')){
                $app->shutdown(function($app){
                        /** @var LaravelDebugbar $debugbar */
                        $debugbar = $app['debugbar'];
                        $debugbar->collectConsole();
                    });
            }else{
                $this->app['config']->set('laravel-debugbar::config.enabled', false);
            }
        }elseif( ! $this->shouldUseMiddleware()){
            $app->after(function ($request, $response) use($app)
            {
                /** @var LaravelDebugbar $debugbar */
                $debugbar = $app['debugbar'];
                $debugbar->modifyResponse($request, $response);
            });
        }

        $this->app['router']->get('_debugbar/open', array('as' => 'debugbar.openhandler', function() use($app){

            // Reflash session data
            $app['session']->reflash();
            
            $debugbar = $app['debugbar'];

            if(!$debugbar->isEnabled()){
                $app->abort('500', 'Debugbar is not enabled');
            }

            $openHandler = new \DebugBar\OpenHandler($debugbar);

            $data = $openHandler->handle(null, false, false);
            return \Response::make($data, 200, array(
                'Content-Type'=> 'application/json'
            ));
        }));

        if($this->app['config']->get('laravel-debugbar::config.enabled'))
        {
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
        $this->app['debugbar'] = $this->app->share(function ($app){
                $debugbar = new LaravelDebugBar($app);

                $sessionManager = $app['session'];
                $httpDriver = new SymfonyHttpDriver($sessionManager);
                $debugbar->setHttpDriver($httpDriver);

                return $debugbar;
            });

        $this->app['command.debugbar.publish'] = $this->app->share(function($app)
            {
                //Make sure the asset publisher is registered.
                $app->register('Illuminate\Foundation\Providers\PublisherServiceProvider');
                return new Console\PublishCommand($app['asset.publisher']);
            });

        $this->app['command.debugbar.clear'] = $this->app->share(function($app)
            {
                return new Console\ClearCommand($app['debugbar']);
            });

        $this->commands(array('command.debugbar.publish', 'command.debugbar.clear'));

        if($this->shouldUseMiddleware()){
            $this->app->middleware('Barryvdh\Debugbar\Middleware', array($this->app));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('debugbar', 'command.debugbar.publish', 'command.debugbar.clear');
    }

    protected function shouldUseMiddleware(){
        $app = $this->app;
        return !$app->runningInConsole() && version_compare($app::VERSION, '4.1', '>=');
    }

}
