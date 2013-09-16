<?php namespace Barryvdh\Debugbar;

use DebugBar\DebugBar;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\Bridge\MonologCollector;
use DebugBar\Bridge\Twig\TraceableTwigEnvironment;
use DebugBar\Bridge\Twig\TwigCollector;
use Barryvdh\Debugbar\DataCollector\LaravelCollector;
use Barryvdh\Debugbar\DataCollector\ViewCollector;
use Barryvdh\Debugbar\DataCollector\SymfonyRequestCollector;
use Illuminate\Http\Response;
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
            $debugbar = $this->app['debugbar'];
            $this->addListener();
        }


    }

    public function collects($name, $default=false){
        return $this->app['config']->get('laravel-debugbar::config.collectors.'.$name, $default);
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

                $debugbar = new LaravelDebugBar;

                if($app['config']->get('laravel-debugbar::config.enabled')){

                    if($self->collects('phpinfo', true)){
                        $debugbar->addCollector(new PhpInfoCollector());
                    }
                    if($self->collects('messages', true)){
                        $debugbar->addCollector(new MessagesCollector());
                    }
                    if($self->collects('time', true)){
                        $debugbar->addCollector(new TimeDataCollector());
                    }
                    if($self->collects('memory', true)){
                        $debugbar->addCollector(new MemoryCollector());
                    }
                    if($self->collects('exceptions', true)){
                        $debugbar->addCollector(new ExceptionsCollector());
                    }
                    if($self->collects('laravel', false)){
                        $debugbar->addCollector(new LaravelCollector());
                    }
                    if($self->collects('default_request', false)){
                        $debugbar->addCollector(new RequestDataCollector());
                    }

                    if($self->collects('events', false)  and isset($app['events'])){
                        $debugbar->addCollector(new MessagesCollector('events'));
                        $app['events']->listen('*', function() use($debugbar){
                                $args = func_get_args();
                                $event = end($args);
                                $debugbar['events']->info("Received event: ". $event);
                            });
                    }

                    if($self->collects('views', true)  and isset($app['events'])){
                        $debugbar->addCollector(new ViewCollector());
                        $app['events']->listen('composing:*', function($view) use($debugbar){
                                $debugbar['views']->addView($view);
                            });
                    }

                    if($self->collects('route')){
                        if(class_exists('Illuminate\Routing\RouteCollection')){
                            $debugbar->addCollector($app->make('Barryvdh\Debugbar\DataCollector\IlluminateRouteCollector'));
                        }else{
                            $debugbar->addCollector($app->make('Barryvdh\Debugbar\DataCollector\SymfonyRouteCollector'));
                        }
                    }

                    if( $self->collects('log', true) and isset($app['log']) ){
                        if($self->collects('messages', true)){
                            $logger = new MessagesCollector('log');
                            $debugbar['messages']->aggregate($logger);
                            $app['log']->listen(function($level, $message, $context) use($logger)
                                {
                                    $log = '['.date('H:i:s').'] '. "LOG.$level: " . $message . (!empty($context) ? ' '.json_encode($context) : '');
                                    $logger->addMessage($log, $level);
                                });
                        }else{
                            $debugbar->addCollector(new MonologCollector( $app['log']->getMonolog() ));
                        }
                    }

                    if($self->collects('db', true) and isset($app['db'])){
                        try{
                            $pdo = new TraceablePDO( $app['db']->getPdo() );
                            $debugbar->addCollector(new PDOCollector( $pdo ));
                        }catch(\PDOException $e){
                            //Not connection set..
                        }
                    }

                    if($self->collects('twig') and isset($app['twig'])){
                        $time = isset($debugbar['time']) ? $debugbar['time'] : null;
                        $app['twig'] = new TraceableTwigEnvironment($app['twig'], $time);
                        //If we already collect Views, skip the collector (but do add timing)
                        if(!$self->collects('views', true)){
                            $debugbar->addCollector(new TwigCollector($app['twig']));
                        }
                    }
                }

                return $debugbar;
            });



        $this->app['debugbar.renderer'] = $this->app->share(function ($app) {

                /** @var \DebugBar\StandardDebugBar $debugbar */
                $debugbar = $app['debugbar'];
                $renderer = $debugbar->getJavascriptRenderer();
                $renderer->setBaseUrl(asset('packages/barryvdh/laravel-debugbar'));
                $renderer->setIncludeVendors($app['config']->get('laravel-debugbar::config.include_vendors', true));

                return $renderer;
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
        return array('debugbar', 'debugbar.renderer', 'command.debugbar.publish');
    }


    protected function addListener(){

        $app = $this->app;
        $self = $this;

        $this->app['router']->after(function ($request, $response) use($app, $self)
            {
                if( $app->runningInConsole() or $response->isRedirection()){
                    return;
                }

                $debugbar = $app['debugbar'];

                if($app['config']->get('laravel-debugbar::config.collectors.symfony_request', true)){
                    $debugbar->addCollector(new SymfonyRequestCollector($request, $response, $app->make('Symfony\Component\HttpKernel\DataCollector\RequestDataCollector')));
                }


                if( $request->isXmlHttpRequest()
                    || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
                    || 'html' !== $request->getRequestFormat()
                ){
                    $debugbar->addDataToHeaders($response);
                }else{
                    $self->injectDebugbar($response);
                }


            });
    }

    /**
     * Injects the web debug toolbar into the given Response.
     *
     * @param Response $response A Response instance
     * Source: https://github.com/symfony/WebProfilerBundle/blob/master/EventListener/WebDebugToolbarListener.php
     */
    public function injectDebugbar(Response $response)
    {
        if (function_exists('mb_stripos')) {
            $posrFunction   = 'mb_strripos';
            $substrFunction = 'mb_substr';
        } else {
            $posrFunction   = 'strripos';
            $substrFunction = 'substr';
        }

        $content = $response->getContent();
        $pos = $posrFunction($content, '</body>');



        $renderer = $this->app['debugbar.renderer'];
        $debugbar = $renderer->renderHead() . $renderer->render();

        if (false !== $pos) {
            $content = $substrFunction($content, 0, $pos).$debugbar.$substrFunction($content, $pos);
        }else{
            $content = $content . $debugbar;
        }

        $response->setContent($content);
    }
}
