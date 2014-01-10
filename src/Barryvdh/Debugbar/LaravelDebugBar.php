<?php namespace Barryvdh\Debugbar;

use Exception;
use DebugBar\DebugBar;

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
use Barryvdh\Debugbar\DataCollector\FilesCollector;
use Barryvdh\Debugbar\DataCollector\LogsCollector;
use Barryvdh\Debugbar\DataCollector\ConfigCollector;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Debug bar subclass which adds all without Request and with LaravelCollector.
 * Rest is added in Service Provider
 *
 * @method void emergency($message)
 * @method void alert($message)
 * @method void critical($message)
 * @method void error($message)
 * @method void warning($message)
 * @method void notice($message)
 * @method void info($message)
 * @method void debug($message)
 * @method void log($message)
 */
class LaravelDebugbar extends DebugBar
{
    /**
     * The Laravel application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * True when booted.
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct($app=null){
        if(!$app){
            $app = app();   //Fallback when $app is not given
        }
        $this->app = $app;
    }

    /**
     * Enable the Debugbar and boot, if not already booted.
     */
    public function enable(){
        $this->app['config']->set('laravel-debugbar::config.enabled', true);
        if(!$this->booted){
            $this->boot();
        }
    }

    /**
     * Disable the Debugbar
     */
    public function disable(){
        $this->app['config']->set('laravel-debugbar::config.enabled', false);
    }


    /**
     * Boot the debugbar (add collectors, renderer and listener)
     */
    public function boot(){
        if($this->booted){
            return;
        }

        $debugbar = $this;
        $app = $this->app;
        if($this->shouldCollect('phpinfo', true)){
            $this->addCollector(new PhpInfoCollector());
        }
        if($this->shouldCollect('messages', true)){
            $this->addCollector(new MessagesCollector());
        }
        if($this->shouldCollect('time', true)){

            $this->addCollector(new TimeDataCollector());

            $this->app->booted(function() use($debugbar)
                {
                    if(defined('LARAVEL_START')){
                        $debugbar['time']->addMeasure('Booting', LARAVEL_START, microtime(true));
                    }
                });

            //Check if App::before is already called..
            if(version_compare($app::VERSION, '4.1', '>=') && $this->app->isBooted()){
                $debugbar->startMeasure('application', 'Application');
            }else{
                $this->app->before(function() use($debugbar)
                    {
                        $debugbar->startMeasure('application', 'Application');
                    });
            }

            $this->app->after(function() use($debugbar)
            {
                $debugbar->stopMeasure('application');
                $debugbar->startMeasure('after', 'After application');
            });

        }
        if($this->shouldCollect('memory', true)){
            $this->addCollector(new MemoryCollector());
        }
        if($this->shouldCollect('exceptions', true)){
            $this->addCollector(new ExceptionsCollector());
        }
        if($this->shouldCollect('laravel', false)){
            $this->addCollector(new LaravelCollector());
        }
        if($this->shouldCollect('default_request', false)){
            $this->addCollector(new RequestDataCollector());
        }

        if($this->shouldCollect('events', false) and isset($this->app['events'])){
            $this->addCollector(new MessagesCollector('events'));
            $dispatcher = $this->app['events'];
            $dispatcher->listen('*', function() use($debugbar, $dispatcher){
                if(method_exists($dispatcher, 'firing')){
                    $event = $dispatcher->firing();
                }else{
                    $args = func_get_args();
                    $event = end($args);
                }
                $debugbar['events']->info("Received event: ". $event);
            });
        }

        if($this->shouldCollect('views', true) and isset($this->app['events'])){
            $collectData = $this->app['config']->get('laravel-debugbar::config.options.views.data', true);
            $this->addCollector(new ViewCollector($collectData));
            $this->app['events']->listen('composing:*', function($view) use($debugbar){
                $debugbar['views']->addView($view);
            });
        }

        if($this->shouldCollect('route')){
            if(version_compare($app::VERSION, '4.1', '>=')){
                $this->addCollector($this->app->make('Barryvdh\Debugbar\DataCollector\IlluminateRouteCollector'));
            }else{
                $this->addCollector($this->app->make('Barryvdh\Debugbar\DataCollector\SymfonyRouteCollector'));
            }
        }

        if( $this->shouldCollect('log', true) ){
            if($this->hasCollector('messages') ){
                $logger = new MessagesCollector('log');
                $this['messages']->aggregate($logger);
                $this->app['log']->listen(function($level, $message, $context) use($logger)
                {
                    $log = '['.date('H:i:s').'] '. "LOG.$level: " . $message . (!empty($context) ? ' '.json_encode($context) : '');
                    $logger->addMessage($log, $level);
                });
            }else{
                $this->addCollector(new MonologCollector( $this->app['log']->getMonolog() ));
            }
        }

        if($this->shouldCollect('db', true) and isset($this->app['db'])){
            try{
                $pdo = new TraceablePDO( $this->app['db']->getPdo() );
                $pdoCollector = new PDOCollector( $pdo );
                if($this->app['config']->get('laravel-debugbar::config.options.pdo.with_params')){
                    $pdoCollector->setRenderSqlWithParams(true, $this->app['config']->get('laravel-debugbar::config.options.pdo.quotation_char'));
                }

                foreach($this->app['config']->get('laravel-debugbar::config.options.pdo.extra_connections', array()) as $name){
                    try{
                        $pdo = new TraceablePDO($this->app['db']->connection($name)->getPdo());
                        $pdoCollector->addConnection($pdo, $name);
                    }catch(\Exception $e){
                        if($this->hasCollector('exceptions')){
                            $this['exceptions']->addException($e);
                        }elseif($this->hasCollector('messages')){
                            $this['messages']->error($e->getMessage());
                        }
                    }
                }
                $this->addCollector($pdoCollector);
            }catch(\PDOException $e){
                //Not connection set..
            }
        }

        if($this->shouldCollect('twig') and isset($this->app['twig'])){
            $time = isset($this['time']) ? $this['time'] : null;
            $this->app['twig'] = new TraceableTwigEnvironment($this->app['twig'], $time);
            //If we already collect Views, skip the collector (but do add timing)
            if(!$this->hasCollector('views')){
                $this->addCollector(new TwigCollector($this->app['twig']));
            }
        }


        if($this->shouldCollect('mail', true)){
            $mailer = $this->app['mailer']->getSwiftMailer();
            $this->addCollector(new SwiftMailCollector($mailer));
            if($this->app['config']->get('laravel-debugbar::config.options.mail.full_log') and $this->hasCollector('messages')){
                $this['messages']->aggregate(new SwiftLogCollector($mailer));
            }
        }

        if($this->shouldCollect('config', false)){
            $this->addCollector(new ConfigCollector());
        }

        if($this->shouldCollect('logs', false)){
            $file = $this->app['config']->get('laravel-debugbar::config.options.logs.file');
            $this->addCollector(new LogsCollector($file));
        }
        if($this->shouldCollect('files', false)){
            $this->addCollector(new FilesCollector());
        }

        $renderer = $this->getJavascriptRenderer();
        $renderer->setBaseUrl(asset('packages/barryvdh/laravel-debugbar'));
        $renderer->setIncludeVendors($this->app['config']->get('laravel-debugbar::config.include_vendors', true));

        $this->booted = true;

    }

    public function modifyResponse($request, $response){
        $app = $this->app;
        if( $app->runningInConsole() or (!$app['config']->get('laravel-debugbar::config.enabled')) ){
            return $response;
        }

        /** @var \Illuminate\Session\SessionManager $sessionManager */
        $sessionManager = $app['session'];
        $httpDriver = new SymfonyHttpDriver($sessionManager, $response);
        $this->setHttpDriver($httpDriver);

        if($this->shouldCollect('symfony_request', true) and !$this->hasCollector('request')){
            $this->addCollector(new SymfonyRequestCollector($request, $response, $app['session'], $app->make('Symfony\Component\HttpKernel\DataCollector\RequestDataCollector')));
        }

        if($response->isRedirection()){
            $this->stackData();
        }elseif( $request->isXmlHttpRequest() and $app['config']->get('laravel-debugbar::config.capture_ajax', true)){
            $this->sendDataInHeaders();
        }elseif(
            ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
            || 'html' !== $request->getRequestFormat()
        ){
            //Do nothing
        }elseif($app['config']->get('laravel-debugbar::config.inject', true)){
            $this->injectDebugbar($response);
        }
        return $response;
    }

    public function shouldCollect($name, $default=false){
        return $this->app['config']->get('laravel-debugbar::config.collectors.'.$name, $default);
    }


    /**
     * Starts a measure
     *
     * @param string $name Internal name, used to stop the measure
     * @param string $label Public name
     */
    public function startMeasure($name, $label=null){
        if($this->hasCollector('time')){
            /** @var \DebugBar\DataCollector\TimeDataCollector $collector */
            $collector = $this->getCollector('time');
            $collector->startMeasure($name, $label);
        }
    }

    /**
     * Stops a measure
     *
     * @param string $name
     */
    public function stopMeasure($name)
    {
        if($this->hasCollector('time')){
            /** @var \DebugBar\DataCollector\TimeDataCollector $collector */
            $collector = $this->getCollector('time');
            try{
                $collector->stopMeasure($name);
            }catch(\Exception $e){
              //  $this->addException($e);
            }

        }
    }

    /**
     * Adds a measure
     *
     * @param string $label
     * @param float $start
     * @param float $end
     */
    public function addMeasure($label, $start, $end)
    {
        if($this->hasCollector('time')){
            /** @var \DebugBar\DataCollector\TimeDataCollector $collector */
            $collector = $this->getCollector('time');
            $collector->addMeasure($label, $start, $end);
        }
    }

    /**
     * Utility function to measure the execution of a Closure
     *
     * @param string $label
     * @param \Closure|callable $closure
     */
    public function measure($label, \Closure $closure)
    {
        if($this->hasCollector('time')){
            /** @var \DebugBar\DataCollector\TimeDataCollector $collector */
            $collector = $this->getCollector('time');
            $collector->measure($label, $closure);
        }
    }

    /**
     * Adds an exception to be profiled in the debug bar
     *
     * @param Exception $e
     */
    public function addException(Exception $e)
    {
        if($this->hasCollector('exceptions')){
            /** @var \DebugBar\DataCollector\ExceptionsCollector $collector */
            $collector = $this->getCollector('exceptions');
            $collector->addException($e);
        }
    }

    /**
     * Adds a message to the MessagesCollector
     *
     * A message can be anything from an object to a string
     *
     * @param mixed $message
     * @param string $label
     */
    public function addMessage($message, $label = 'info')
    {
        if($this->hasCollector('messages')){
            /** @var \DebugBar\DataCollector\MessagesCollector $collector */
            $collector = $this->getCollector('messages');
            $collector->addMessage($message, $label);
        }
    }

    /**
     * Injects the web debug toolbar into the given Response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response A Response instance
     * Based on https://github.com/symfony/WebProfilerBundle/blob/master/EventListener/WebDebugToolbarListener.php
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

        $renderer = $this->getJavascriptRenderer();
        $debugbar = $renderer->renderHead() . $renderer->render();

        if (false !== $pos) {
            $content = $substrFunction($content, 0, $pos).$debugbar.$substrFunction($content, $pos);
        }else{
            $content = $content . $debugbar;
        }

        $response->setContent($content);
    }

    /**
     * Magic calls for adding messages
     *
     * @param string $method
     * @param array $args
     * @return mixed|void
     */
    public function __call($method, $args)
    {
        $messageLevels = array('emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug', 'log');
        if(in_array($method, $messageLevels)){
            $this->addMessage($args[0], $method);
        }
    }

}