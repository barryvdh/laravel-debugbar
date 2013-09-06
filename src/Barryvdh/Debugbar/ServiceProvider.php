<?php namespace Barryvdh\Debugbar;

use DebugBar\StandardDebugBar;
use DebugBar\Bridge\MonologCollector;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\Bridge\SwiftMailer\SwiftLogCollector;
use DebugBar\Bridge\SwiftMailer\SwiftMailCollector;
use DebugBar\DataCollector\MessagesCollector;
use Barryvdh\Debugbar\DataCollector\ViewCollector;
use Barryvdh\Debugbar\DataCollector\RouteCollector;
use Barryvdh\Debugbar\DataCollector\LaravelCollector;
use Monolog\Logger;
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

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->package('barryvdh/laravel-debugbar');

        $this->app['debugbar'] = $this->app->share(function ($app) {

                $debugbar = new StandardDebugBar;

                $events = $app['events'];

                $debugbar->addCollector(new LaravelCollector());

                if($app['config']->get('laravel-debugbar::config.log_events', false)){
                    $debugbar->addCollector(new MessagesCollector('events'));
                    $events->listen('*', function() use($debugbar){
                            $args = func_get_args();
                            $event = end($args);
                            $debugbar['events']->info("Received event: ". $event);
                        });
                }

                $debugbar->addCollector(new ViewCollector());
                $events->listen('composing:*', function($view) use($debugbar){
                        $debugbar['views']->addView($view);
                    });

                if(class_exists('Illuminate\Routing\RouteCollection')){
                    $debugbar->addCollector($app->make('Barryvdh\Debugbar\DataCollector\IlluminateRouteCollector'));
                }else{
                    $debugbar->addCollector($app->make('Barryvdh\Debugbar\DataCollector\SymfonyRouteCollector'));
                }

                if($log = $app['log']){
                    $debugbar->addCollector(new MonologCollector( $log->getMonolog(), Logger::DEBUG, true, 'Log' ));
                }

                if($db = $app['db']){
                    try{
                        $pdo = new TraceablePDO( $db->getPdo() );
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
        $this->app['router']->after(function (Request $request, Response $response) use($app)
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