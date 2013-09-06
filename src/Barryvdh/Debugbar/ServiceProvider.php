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
use Barryvdh\Debugbar\DataCollector\LaravelCollector;
use Barryvdh\Debugbar\DataCollector\ViewCollector;
use Barryvdh\Debugbar\DataCollector\SymfonyRequestCollector;
use Illuminate\Http\Request;
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
        $debugbar = $this->app['debugbar'];
        $this->addListener();
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

        $this->app['debugbar'] = $this->app->share(function ($app) {

                $debugbar = new DebugBar;

                if($this->collects('phpinfo', true)){
                    $debugbar->addCollector(new PhpInfoCollector());
                }
                if($this->collects('messages', true)){
                    $debugbar->addCollector(new MessagesCollector());
                }
                if($this->collects('time', true)){
                    $debugbar->addCollector(new TimeDataCollector());
                }
                if($this->collects('memory', true)){
                    $debugbar->addCollector(new MemoryCollector());
                }
                if($this->collects('exceptions', true)){
                    $debugbar->addCollector(new ExceptionsCollector());
                }
                if($this->collects('laravel', false)){
                    $debugbar->addCollector(new LaravelCollector());
                }
                if($this->collects('default_request', false)){
                    $debugbar->addCollector(new RequestDataCollector());
                }

                if($this->collects('events', false)){
                    $debugbar->addCollector(new MessagesCollector('events'));
                    $app['events']->listen('*', function() use($debugbar){
                            $args = func_get_args();
                            $event = end($args);
                            $debugbar['events']->info("Received event: ". $event);
                        });
                }

                if($this->collects('views', true)){
                    $debugbar->addCollector(new ViewCollector());
                    $app['events']->listen('composing:*', function($view) use($debugbar){
                            $debugbar['views']->addView($view);
                        });
                }

                if($this->collects('route')){
                    if(class_exists('Illuminate\Routing\RouteCollection')){
                        $debugbar->addCollector($app->make('Barryvdh\Debugbar\DataCollector\IlluminateRouteCollector'));
                    }else{
                        $debugbar->addCollector($app->make('Barryvdh\Debugbar\DataCollector\SymfonyRouteCollector'));
                    }
                }

                if($this->collects('log', true)){
                    if($this->collects('messages', true)){
                        $app['log']->listen(function($level, $message, $context) use($debugbar)
                            {
                                $log = '['.date('H:i:s').'] '. "LOG.$level: " . $message . (!empty($context) ? ' '.json_encode($context) : '');
                                $debugbar['messages']->addMessage($log, $level);
                            });
                    }else{
                        $debugbar->addCollector(new MonologCollector( $app['log']->getMonolog() ));
                    }
                }

                if($this->collects('db')){
                    try{
                        $pdo = new TraceablePDO( $app['db']->getPdo() );
                        $debugbar->addCollector(new PDOCollector( $pdo ));
                    }catch(\PDOException $e){
                        //Not connection set..
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


    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('debugbar', 'debugbar.renderer');
    }


    protected function addListener(){

        $app = $this->app;
        $this->app['router']->after(function ($request, $response) use($app) 
            {
                if(
                    $app['config']->get('laravel-debugbar::config.enabled') === false
                    || $app->runningInConsole()
                    || $request->isXmlHttpRequest()
                    || $response->isRedirection()
                    || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
                    || 'html' !== $request->getRequestFormat()
                ){
                    return;
                }

                if($app['config']->get('laravel-debugbar::config.collectors.symfony_request', true)){
                    $debugbar = $app['debugbar'];
                    $debugbar->addCollector(new SymfonyRequestCollector($request, $response, $app->make('Symfony\Component\HttpKernel\DataCollector\RequestDataCollector')));
                }

                $this->injectDebugbar($response);

            });
    }

    /**
     * Injects the web debug toolbar into the given Response.
     *
     * @param Response $response A Response instance
     * Source: https://github.com/symfony/WebProfilerBundle/blob/master/EventListener/WebDebugToolbarListener.php
     */
    protected function injectDebugbar(Response $response)
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