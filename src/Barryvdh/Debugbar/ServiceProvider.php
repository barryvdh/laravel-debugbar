<?php namespace Barryvdh\Debugbar;

use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\Bridge\SwiftMailer\SwiftLogCollector;
use DebugBar\Bridge\SwiftMailer\SwiftMailCollector;
use DebugBar\Bridge\MonologCollector;
use DebugBar\Bridge\Twig\TraceableTwigEnvironment;
use DebugBar\Bridge\Twig\TwigCollector;
use Barryvdh\Debugbar\DataCollector\LaravelCollector;
use Barryvdh\Debugbar\DataCollector\ViewCollector;
use Barryvdh\Debugbar\DataCollector\SymfonyRequestCollector;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

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



            if($this->app['config']->get('laravel-debugbar::config.enabled')){

                if($this->collects('phpinfo', true)){
                    $debugbar->addCollector(new PhpInfoCollector());
                }
                if($this->collects('messages', true)){
                    $debugbar->addCollector(new MessagesCollector());
                }
                if($this->collects('time', true)){

                    $debugbar->addCollector(new TimeDataCollector());

                    $this->app->booted(function() use($debugbar)
                        {
                            if(defined('LARAVEL_START')){
                                $debugbar['time']->addMeasure('Booting', LARAVEL_START, microtime(true));
                            }
                        });

                    $this->app->before(function() use($debugbar)
                        {
                            $debugbar->startMeasure('application', 'Application');
                        });

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

                if($this->collects('events', false) and isset($this->app['events'])){
                    $debugbar->addCollector(new MessagesCollector('events'));
                    $this->app['events']->listen('*', function() use($debugbar){
                            $args = func_get_args();
                            $event = end($args);
                            $debugbar['events']->info("Received event: ". $event);
                        });
                }

                if($this->collects('views', true) and isset($this->app['events'])){
                    $debugbar->addCollector(new ViewCollector());
                    $this->app['events']->listen('composing:*', function($view) use($debugbar){
                            $debugbar['views']->addView($view);
                        });
                }

                if($this->collects('route')){
                    if(class_exists('Illuminate\Routing\RouteCollection')){
                        $debugbar->addCollector($this->app->make('Barryvdh\Debugbar\DataCollector\IlluminateRouteCollector'));
                    }else{
                        $debugbar->addCollector($this->app->make('Barryvdh\Debugbar\DataCollector\SymfonyRouteCollector'));
                    }
                }

                if( $this->collects('log', true) ){
                    if($debugbar->hasCollector('messages') ){
                        $logger = new MessagesCollector('log');
                        $debugbar['messages']->aggregate($logger);
                        $this->app['log']->listen(function($level, $message, $context) use($logger)
                            {
                                $log = '['.date('H:i:s').'] '. "LOG.$level: " . $message . (!empty($context) ? ' '.json_encode($context) : '');
                                $logger->addMessage($log, $level);
                            });
                    }else{
                        $debugbar->addCollector(new MonologCollector( $this->app['log']->getMonolog() ));
                    }
                }

                if($this->collects('db', true) and isset($this->app['db'])){
                    try{
                        $pdo = new TraceablePDO( $this->app['db']->getPdo() );
                        $pdoCollector = new PDOCollector( $pdo );
                        $pdoCollector->setRenderSqlWithParams($this->app['config']->get('laravel-debugbar::config.options.pdo.with_params', true));
                        foreach($this->app['config']->get('laravel-debugbar::config.options.pdo.extra_connections', array()) as $name){
                            try{
                                $pdo = new TraceablePDO($this->app['db']->connection($name)->getPdo());
                                $pdoCollector->addConnection($pdo, $name);
                            }catch(\Exception $e){
                                if($debugbar->hasCollector('exceptions')){
                                    $debugbar['exceptions']->addException($e);
                                }elseif($debugbar->hasCollector('messages')){
                                    $debugbar['messages']->error($e->getMessage());
                                }
                            }
                        }
                        $debugbar->addCollector($pdoCollector);
                    }catch(\PDOException $e){
                        //Not connection set..
                    }
                }

                if($this->collects('twig') and isset($this->app['twig'])){
                    $time = isset($debugbar['time']) ? $debugbar['time'] : null;
                    $this->app['twig'] = new TraceableTwigEnvironment($this->app['twig'], $time);
                    //If we already collect Views, skip the collector (but do add timing)
                    if(!$debugbar->hasCollector('views')){
                        $debugbar->addCollector(new TwigCollector($this->app['twig']));
                    }
                }


                if($this->collects('mail', true)){
                    $mailer = $this->app['mailer']->getSwiftMailer();
                    $debugbar->addCollector(new SwiftMailCollector($mailer));
                    if($this->app['config']->get('laravel-debugbar::config.options.mail.full_log') and $debugbar->hasCollector('messages')){
                        $debugbar['messages']->aggregate(new SwiftLogCollector($mailer));
                    }
                }

                $renderer = $debugbar->getJavascriptRenderer();
                $renderer->setBaseUrl(asset('packages/barryvdh/laravel-debugbar'));
                $renderer->setIncludeVendors($this->app['config']->get('laravel-debugbar::config.include_vendors', true));

            }

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

                $sessionStore = $app['session.store'];
                $httpDriver = new SymfonyHttpDriver($sessionStore);
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


    protected function addListener(){

        $app = $this->app;
        $this->app['router']->after(function (Request $request, Response $response) use($app)
            {

                if( $app->runningInConsole() or (!$app['config']->get('laravel-debugbar::config.enabled')) ){
                    return;
                }

                /** @var LaravelDebugbar $debugbar */
                $debugbar = $app['debugbar'];

                $sessionStore = $app['session.store'];
                $httpDriver = new SymfonyHttpDriver($sessionStore, $response);
                $debugbar->setHttpDriver($httpDriver);

                if($app['config']->get('laravel-debugbar::config.collectors.symfony_request', true)){
                    $debugbar->addCollector(new SymfonyRequestCollector($request, $response, $app->make('Symfony\Component\HttpKernel\DataCollector\RequestDataCollector')));
                }

                if($response->isRedirection()){
                    $debugbar->stackData();
                }elseif( $request->isXmlHttpRequest() and $app['config']->get('laravel-debugbar::config.capture_ajax', true)){
                    $debugbar->sendDataInHeaders();
                }elseif(
                    ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
                    || 'html' !== $request->getRequestFormat()
                ){
                   return;
                }else{
                    $debugbar->injectDebugbar($response);
                }

            });
    }


}
