<?php namespace Barryvdh\Debugbar;

use Barryvdh\Debugbar\DataCollector\AuthCollector;
use Barryvdh\Debugbar\DataCollector\EventCollector;
use Barryvdh\Debugbar\DataCollector\FilesCollector;
use Barryvdh\Debugbar\DataCollector\LaravelCollector;
use Barryvdh\Debugbar\DataCollector\LogsCollector;
use Barryvdh\Debugbar\DataCollector\QueryCollector;
use Barryvdh\Debugbar\DataCollector\SessionCollector;
use Barryvdh\Debugbar\DataCollector\SymfonyRequestCollector;
use Barryvdh\Debugbar\DataCollector\ViewCollector;
use Barryvdh\Debugbar\Storage\FilesystemStorage;
use DebugBar\Bridge\MonologCollector;
use DebugBar\Bridge\SwiftMailer\SwiftLogCollector;
use DebugBar\Bridge\SwiftMailer\SwiftMailCollector;
use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DebugBar;
use Exception;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * Normalized Laravel Version
     *
     * @var string
     */
    protected $version;

    /**
     * True when booted.
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct($app = null)
    {
        if (!$app) {
            $app = app();   //Fallback when $app is not given
        }
        $this->app = $app;
        
        //Normalize Laravel version
        $version = $app::VERSION;
        list($version) = explode('-', $version);
        $this->version = $version;
    }

    /**
     * Enable the Debugbar and boot, if not already booted.
     */
    public function enable()
    {
        $this->app['config']->set('laravel-debugbar::config.enabled', true);
        if (!$this->booted) {
            $this->boot();
        }
    }

    /**
     * Boot the debugbar (add collectors, renderer and listener)
     */
    public function boot()
    {
        if ($this->booted) {
            return;
        }

        if ($this->isDebugbarRequest()) {
            $this->app['session']->reflash();
        }

        /** @var \Barryvdh\Debugbar\LaravelDebugbar $debugbar */
        $debugbar = $this;
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;

        if ($this->app['config']->get('laravel-debugbar::config.storage.enabled')) {
            $path = $this->app['config']->get('laravel-debugbar::config.storage.path');
            $storage = new FilesystemStorage($this->app['files'], $path);
            $debugbar->setStorage($storage);
        }

        if ($this->shouldCollect('phpinfo', true)) {
            $this->addCollector(new PhpInfoCollector());
        }
        if ($this->shouldCollect('messages', true)) {
            $this->addCollector(new MessagesCollector());
        }
        if ($this->shouldCollect('time', true)) {
            $startTime = defined('LARAVEL_START') ? LARAVEL_START : null;
            $this->addCollector(new TimeDataCollector($startTime));

            $this->app->booted(
                function () use ($debugbar, $startTime) {
                    if ($startTime) {
                        $debugbar['time']->addMeasure('Booting', $startTime, microtime(true));
                    }
                }
            );

            //Check if App::before is already called.. 
            if ($this->checkVersion('4.1') && $this->app->isBooted()) {
                $debugbar->startMeasure('application', 'Application');
            } else {
                $this->app['router']->before(
                    function () use ($debugbar) {
                        $debugbar->startMeasure('application', 'Application');
                    }
                );
            }

            $this->app['router']->after(
                function () use ($debugbar) {
                    $debugbar->stopMeasure('application');
                    $debugbar->startMeasure('after', 'After application');
                }
            );
        }
        if ($this->shouldCollect('memory', true)) {
            $this->addCollector(new MemoryCollector());
        }
        if ($this->shouldCollect('exceptions', true)) {
            try {
                $exceptionCollector = new ExceptionsCollector();
                $exceptionCollector->setChainExceptions(
                    $this->app['config']->get('laravel-debugbar::config.options.exceptions.chain', true)
                );
                $this->addCollector($exceptionCollector);
                if ($this->checkVersion('5.0', '<')) {
	                $this->app->error(
	                    function (Exception $exception) use ($exceptionCollector) {
	                        $exceptionCollector->addException($exception);
	                    }
	                );
                }
            } catch (\Exception $e) {
            }
        }
        if ($this->shouldCollect('laravel', false)) {
            $this->addCollector(new LaravelCollector($this->app));
        }
        if ($this->shouldCollect('default_request', false)) {
            $this->addCollector(new RequestDataCollector());
        }

        if ($this->shouldCollect('events', false) and isset($this->app['events'])) {
            try {
                $startTime = defined('LARAVEL_START') ? LARAVEL_START : null;
                $eventCollector = new EventCollector($startTime);
                $this->addCollector($eventCollector);
                $this->app['events']->subscribe($eventCollector);

            } catch (\Exception $e) {
                $this->addException(
                    new Exception(
                        'Cannot add EventCollector to Laravel Debugbar: ' . $e->getMessage(),
                        $e->getCode(),
                        $e
                    )
                );
            }
        }

        if ($this->shouldCollect('views', true) and isset($this->app['events'])) {
            try {
                $collectData = $this->app['config']->get('laravel-debugbar::config.options.views.data', true);
                $this->addCollector(new ViewCollector($collectData));
                $this->app['events']->listen(
                    'composing:*',
                    function ($view) use ($debugbar) {
                        $debugbar['views']->addView($view);
                    }
                );
            } catch (\Exception $e) {
                $this->addException(
                    new Exception(
                        'Cannot add ViewCollector to Laravel Debugbar: ' . $e->getMessage(), $e->getCode(), $e
                    )
                );
            }
        }

        if ($this->shouldCollect('route')) {
            try {
                if ($this->checkVersion('4.1')) {
                    $this->addCollector($this->app->make('Barryvdh\Debugbar\DataCollector\IlluminateRouteCollector'));
                } else {
                    $this->addCollector($this->app->make('Barryvdh\Debugbar\DataCollector\SymfonyRouteCollector'));
                }
            } catch (\Exception $e) {
                $this->addException(
                    new Exception(
                        'Cannot add RouteCollector to Laravel Debugbar: ' . $e->getMessage(),
                        $e->getCode(),
                        $e
                    )
                );
            }
        }

        if ($this->shouldCollect('log', true)) {
            try {
                if ($this->hasCollector('messages')) {
                    $logger = new MessagesCollector('log');
                    $this['messages']->aggregate($logger);
                    $this->app['log']->listen(
                        function ($level, $message, $context) use ($logger) {
                            try {
                                $logMessage = (string) $message;
                                if (mb_check_encoding($logMessage, 'UTF-8')) {
                                    $logMessage .= (!empty($context) ? ' ' . json_encode($context) : '');
                                } else {
                                    $logMessage = "[INVALID UTF-8 DATA]";
                                }
                            } catch (\Exception $e) {
                                $logMessage = "[Exception: " . $e->getMessage() . "]";
                            }
                            $logger->addMessage(
                                '[' . date('H:i:s') . '] ' . "LOG.$level: " . $logMessage,
                                $level,
                                false
                            );
                        }
                    );
                } else {
                    $this->addCollector(new MonologCollector($this->app['log']->getMonolog()));
                }
            } catch (\Exception $e) {
                $this->addException(
                    new Exception(
                        'Cannot add LogsCollector to Laravel Debugbar: ' . $e->getMessage(), $e->getCode(), $e
                    )
                );
            }
        }

        if ($this->shouldCollect('db', true) and isset($this->app['db'])) {
            $db = $this->app['db'];
            if ($debugbar->hasCollector('time') && $this->app['config']->get(
                    'laravel-debugbar::config.options.db.timeline',
                    false
                )
            ) {
                $timeCollector = $debugbar->getCollector('time');
            } else {
                $timeCollector = null;
            }
            $queryCollector = new QueryCollector($timeCollector);

            if ($this->app['config']->get('laravel-debugbar::config.options.db.with_params')) {
                $queryCollector->setRenderSqlWithParams(true);
            }

            if ($this->app['config']->get('laravel-debugbar::config.options.db.backtrace')) {
                $queryCollector->setFindSource(true);
            }
            
            if ($this->app['config']->get('laravel-debugbar::config.options.db.explain.enabled')) {
                $types = $this->app['config']->get('laravel-debugbar::config.options.db.explain.types');
                $queryCollector->setExplainSource(true, $types);
            }

            if ($this->app['config']->get('laravel-debugbar::config.options.db.hints', true)) {
                $queryCollector->setShowHints(true);
            }

            $this->addCollector($queryCollector);

            try {
                $db->listen(
                    function ($query, $bindings, $time, $connectionName) use ($db, $queryCollector) {
                        $connection = $db->connection($connectionName);
                        $queryCollector->addQuery((string) $query, $bindings, $time, $connection);
                    }
                );
            } catch (\Exception $e) {
                $this->addException(
                    new Exception(
                        'Cannot add listen to Queries for Laravel Debugbar: ' . $e->getMessage(),
                        $e->getCode(),
                        $e
                    )
                );
            }
        }

        if ($this->shouldCollect('mail', true)) {
            try {
                $mailer = $this->app['mailer']->getSwiftMailer();
                $this->addCollector(new SwiftMailCollector($mailer));
                if ($this->app['config']->get('laravel-debugbar::config.options.mail.full_log') and $this->hasCollector(
                        'messages'
                    )
                ) {
                    $this['messages']->aggregate(new SwiftLogCollector($mailer));
                }
            } catch (\Exception $e) {
                $this->addException(
                    new Exception(
                        'Cannot add MailCollector to Laravel Debugbar: ' . $e->getMessage(), $e->getCode(), $e
                    )
                );
            }
        }

        if ($this->shouldCollect('logs', false)) {
            try {
                $file = $this->app['config']->get('laravel-debugbar::config.options.logs.file');
                $this->addCollector(new LogsCollector($file));
            } catch (\Exception $e) {
                $this->addException(
                    new Exception(
                        'Cannot add LogsCollector to Laravel Debugbar: ' . $e->getMessage(), $e->getCode(), $e
                    )
                );
            }
        }
        if ($this->shouldCollect('files', false)) {
            $this->addCollector(new FilesCollector($app));
        }

        if ($this->shouldCollect('auth', false)) {
            try {
                $authCollector = new AuthCollector($app['auth']);
                $authCollector->setShowName(
                    $this->app['config']->get('laravel-debugbar::config.options.auth.show_name')
                );
                $this->addCollector($authCollector);
            } catch (\Exception $e) {
                $this->addException(
                    new Exception(
                        'Cannot add AuthCollector to Laravel Debugbar: ' . $e->getMessage(), $e->getCode(), $e
                    )
                );
            }
        }

        $renderer = $this->getJavascriptRenderer();
        $renderer->setIncludeVendors($this->app['config']->get('laravel-debugbar::config.include_vendors', true));
        $renderer->setBindAjaxHandlerToXHR($app['config']->get('laravel-debugbar::config.capture_ajax', true));

        $this->booted = true;
    }

    public function shouldCollect($name, $default = false)
    {
        return $this->app['config']->get('laravel-debugbar::config.collectors.' . $name, $default);
    }

    /**
     * Starts a measure
     *
     * @param string $name Internal name, used to stop the measure
     * @param string $label Public name
     */
    public function startMeasure($name, $label = null)
    {
        if ($this->hasCollector('time')) {
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
        if ($this->hasCollector('time')) {
            /** @var \DebugBar\DataCollector\TimeDataCollector $collector */
            $collector = $this->getCollector('time');
            try {
                $collector->stopMeasure($name);
            } catch (\Exception $e) {
                //  $this->addException($e);
            }
        }
    }

    /**
     * Adds an exception to be profiled in the debug bar
     *
     * @param Exception $e
     */
    public function addException(Exception $e)
    {
        if ($this->hasCollector('exceptions')) {
            /** @var \DebugBar\DataCollector\ExceptionsCollector $collector */
            $collector = $this->getCollector('exceptions');
            $collector->addException($e);
        }
    }

    /**
     * Returns a JavascriptRenderer for this instance
     *
     * @param string $baseUrl
     * @param string $basePathng
     * @return JavascriptRenderer
     */
    public function getJavascriptRenderer($baseUrl = null, $basePath = null)
    {
        if ($this->jsRenderer === null) {
            $this->jsRenderer = new JavascriptRenderer($this, $baseUrl, $basePath);
            $this->jsRenderer->setUrlGenerator($this->app['url']);
        }
        return $this->jsRenderer;
    }

    /**
     * Modify the response and inject the debugbar (or data in headers)
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Symfony\Component\HttpFoundation\Response $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function modifyResponse($request, $response)
    {
        $app = $this->app;
        if ($app->runningInConsole() or !$this->isEnabled() || $this->isDebugbarRequest()) {
            return $response;
        }

        if ($this->shouldCollect('config', false)) {
            try {
                $configCollector = new ConfigCollector();
                $configCollector->setData($app['config']->getItems());
                $this->addCollector($configCollector);
            } catch (\Exception $e) {
                $this->addException(
                    new Exception(
                        'Cannot add ConfigCollector to Laravel Debugbar: ' . $e->getMessage(),
                        $e->getCode(),
                        $e
                    )
                );
            }
        }

        /** @var \Illuminate\Session\SessionManager $sessionManager */
        $sessionManager = $app['session'];
        $httpDriver = new SymfonyHttpDriver($sessionManager, $response);
        $this->setHttpDriver($httpDriver);

        if ($this->shouldCollect('session')) {
            try {
                $this->addCollector(new SessionCollector($sessionManager));
            } catch (\Exception $e) {
                $this->addException(
                    new Exception(
                        'Cannot add SessionCollector to Laravel Debugbar: ' . $e->getMessage(),
                        $e->getCode(),
                        $e
                    )
                );
            }
        }

        if ($this->shouldCollect('symfony_request', true) and !$this->hasCollector('request')) {
            try {
                $this->addCollector(new SymfonyRequestCollector($request, $response, $sessionManager));
            } catch (\Exception $e) {
                $this->addException(
                    new Exception(
                        'Cannot add SymfonyRequestCollector to Laravel Debugbar: ' . $e->getMessage(),
                        $e->getCode(),
                        $e
                    )
                );
            }
        }

        if ($response->isRedirection() || !($request instanceof \Illuminate\Http\Request)) {
            try {
                $this->stackData();
            } catch (\Exception $e) {
                $app['log']->error('Debugbar exception: ' . $e->getMessage());
            }
        } elseif (
            ($request->isXmlHttpRequest() || $request->wantsJson()) and
            $app['config']->get('laravel-debugbar::config.capture_ajax', true)
        ) {
            try {
                $this->sendDataInHeaders(true);
            } catch (\Exception $e) {
                $app['log']->error('Debugbar exception: ' . $e->getMessage());
            }
        } elseif (
            ($response->headers->has('Content-Type') and
            strpos($response->headers->get('Content-Type'), 'html') === false)
            || 'html' !== $request->format()
        ) {
            try {
                // Just collect + store data, don't inject it.
                $this->collect();
            } catch (\Exception $e) {
                $app['log']->error('Debugbar exception: ' . $e->getMessage());
            }
        } elseif ($app['config']->get('laravel-debugbar::config.inject', true)) {
            try {
                $this->injectDebugbar($response);
            } catch (\Exception $e) {
                $app['log']->error('Debugbar exception: ' . $e->getMessage());
            }
        }

        // Stop further rendering (on subrequests etc)
        $this->disable();

        return $response;
    }

    /**
     * Check if the Debugbar is enabled
     * @return boolean
     */
    public function isEnabled()
    {
        return value($this->app['config']->get('laravel-debugbar::config.enabled'));
    }

    /**
     * Check if this is a request to the Debugbar OpenHandler
     *
     * @return bool
     */
    protected function isDebugbarRequest()
    {
        return $this->app['request']->segment(1) == '_debugbar';
    }

    /**
     * Collects the data from the collectors
     *
     * @return array
     */
    public function collect()
    {
        /** @var Request $request */
        $request = $this->app['request'];

        $this->data = array(
            '__meta' => array(
                'id' => $this->getCurrentRequestId(),
                'datetime' => date('Y-m-d H:i:s'),
                'utime' => microtime(true),
                'method' => $request->getMethod(),
                'uri' => $request->getRequestUri(),
                'ip' => $request->getClientIp()
            )
        );

        foreach ($this->collectors as $name => $collector) {
            $this->data[$name] = $collector->collect();
        }

        // Remove all invalid (non UTF-8) characters
        array_walk_recursive(
            $this->data,
            function (&$item) {
                if (is_string($item) && !mb_check_encoding($item, 'UTF-8')) {
                    $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
                }
            }
        );

        if ($this->storage !== null) {
            $this->storage->save($this->getCurrentRequestId(), $this->data);
        }

        return $this->data;
    }

    /**
     * Injects the web debug toolbar into the given Response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response A Response instance
     * Based on https://github.com/symfony/WebProfilerBundle/blob/master/EventListener/WebDebugToolbarListener.php
     */
    public function injectDebugbar(Response $response)
    {
        $content = $response->getContent();

        $renderer = $this->getJavascriptRenderer();
        if ($this->getStorage()) {
            $openHandlerUrl = $this->app['url']->route('debugbar.openhandler');
            $renderer->setOpenHandlerUrl($openHandlerUrl);
        }

        $renderedContent = $renderer->renderHead() . $renderer->render();

        $pos = strripos($content, '</body>');
        if (false !== $pos) {
            $content = substr($content, 0, $pos) . $renderedContent . substr($content, $pos);
        } else {
            $content = $content . $renderedContent;
        }

        $response->setContent($content);
    }

    /**
     * Disable the Debugbar
     */
    public function disable()
    {
        $this->app['config']->set('laravel-debugbar::config.enabled', false);
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
        if ($this->hasCollector('time')) {
            /** @var \DebugBar\DataCollector\TimeDataCollector $collector */
            $collector = $this->getCollector('time');
            $collector->addMeasure($label, $start, $end);
        }
    }

    /**
     * Utility function to measure the execution of a Closure
     *
     * @param string $label
     * @param \Closure $closure
     */
    public function measure($label, \Closure $closure)
    {
        if ($this->hasCollector('time')) {
            /** @var \DebugBar\DataCollector\TimeDataCollector $collector */
            $collector = $this->getCollector('time');
            $collector->measure($label, $closure);
        } else {
            $closure();
        }
    }

    /**
     * Collect data in a CLI request
     *
     * @return array
     */
    public function collectConsole()
    {
        if (!$this->isEnabled()) {
            return;
        }

        $this->data = array(
            '__meta' => array(
                'id' => $this->getCurrentRequestId(),
                'datetime' => date('Y-m-d H:i:s'),
                'utime' => microtime(true),
                'method' => 'CLI',
                'uri' => isset($_SERVER['argv']) ? implode(' ', $_SERVER['argv']) : null,
                'ip' => isset($_SERVER['SSH_CLIENT']) ? $_SERVER['SSH_CLIENT'] : null
            )
        );

        foreach ($this->collectors as $name => $collector) {
            $this->data[$name] = $collector->collect();
        }

        // Remove all invalid (non UTF-8) characters
        array_walk_recursive(
            $this->data,
            function (&$item) {
                if (is_string($item) && !mb_check_encoding($item, 'UTF-8')) {
                    $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
                }
            }
        );

        if ($this->storage !== null) {
            $this->storage->save($this->getCurrentRequestId(), $this->data);
        }

        return $this->data;
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
        if (in_array($method, $messageLevels)) {
            $this->addMessage($args[0], $method);
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
        if ($this->hasCollector('messages')) {
            /** @var \DebugBar\DataCollector\MessagesCollector $collector */
            $collector = $this->getCollector('messages');
            $collector->addMessage($message, $label);
        }
    }
    
    /**
     * Check the version of Laravel
     *
     * @param string $version
     * @param string $operator (default: '>=')
     * @return boolean
     */
    protected function checkVersion($version, $operator = ">=")
    {
    	return version_compare($this->version, $version, $operator);
    }
}
