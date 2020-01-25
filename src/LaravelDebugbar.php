<?php namespace Barryvdh\Debugbar;

use Barryvdh\Debugbar\DataCollector\AuthCollector;
use Barryvdh\Debugbar\DataCollector\CacheCollector;
use Barryvdh\Debugbar\DataCollector\EventCollector;
use Barryvdh\Debugbar\DataCollector\FilesCollector;
use Barryvdh\Debugbar\DataCollector\GateCollector;
use Barryvdh\Debugbar\DataCollector\LaravelCollector;
use Barryvdh\Debugbar\DataCollector\LogsCollector;
use Barryvdh\Debugbar\DataCollector\ModelsCollector;
use Barryvdh\Debugbar\DataCollector\MultiAuthCollector;
use Barryvdh\Debugbar\DataCollector\QueryCollector;
use Barryvdh\Debugbar\DataCollector\SessionCollector;
use Barryvdh\Debugbar\DataCollector\RequestCollector;
use Barryvdh\Debugbar\DataCollector\ViewCollector;
use Barryvdh\Debugbar\Storage\FilesystemStorage;
use DebugBar\Bridge\MonologCollector;
use DebugBar\Bridge\SwiftMailer\SwiftLogCollector;
use DebugBar\Bridge\SwiftMailer\SwiftMailCollector;
use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use Barryvdh\Debugbar\DataFormatter\QueryFormatter;
use Barryvdh\Debugbar\Support\Clockwork\ClockworkCollector;
use DebugBar\DebugBar;
use DebugBar\Storage\PdoStorage;
use DebugBar\Storage\RedisStorage;
use Exception;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Debug bar subclass which adds all without Request and with LaravelCollector.
 * Rest is added in Service Provider
 *
 * @method void emergency(...$message)
 * @method void alert(...$message)
 * @method void critical(...$message)
 * @method void error(...$message)
 * @method void warning(...$message)
 * @method void notice(...$message)
 * @method void info(...$message)
 * @method void debug(...$message)
 * @method void log(...$message)
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
     * True when enabled, false disabled an null for still unknown
     *
     * @var bool
     */
    protected $enabled = null;

    /**
     * True when this is a Lumen application
     *
     * @var bool
     */
    protected $is_lumen = false;

    /**
     * @param Application $app
     */
    public function __construct($app = null)
    {
        if (!$app) {
            $app = app();   //Fallback when $app is not given
        }
        $this->app = $app;
        $this->version = $app->version();
        $this->is_lumen = Str::contains($this->version, 'Lumen');
    }

    /**
     * Enable the Debugbar and boot, if not already booted.
     */
    public function enable()
    {
        $this->enabled = true;

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

        /** @var \Barryvdh\Debugbar\LaravelDebugbar $debugbar */
        $debugbar = $this;

        /** @var Application $app */
        $app = $this->app;

        // Set custom error handler
        if ($app['config']->get('debugbar.error_handler' , false)) {
            set_error_handler([$this, 'handleError']);
        }

        $this->selectStorage($debugbar);

        if ($this->shouldCollect('phpinfo', true)) {
            $this->addCollector(new PhpInfoCollector());
        }

        if ($this->shouldCollect('messages', true)) {
            $this->addCollector(new MessagesCollector());
        }

        if ($this->shouldCollect('time', true)) {
            $this->addCollector(new TimeDataCollector());

            if ( ! $this->isLumen()) {
                $this->app->booted(
                    function () use ($debugbar) {
                        $startTime = $this->app['request']->server('REQUEST_TIME_FLOAT');
                        if ($startTime) {
                            $debugbar['time']->addMeasure('Booting', $startTime, microtime(true));
                        }
                    }
                );
            }

            $debugbar->startMeasure('application', 'Application');
        }

        if ($this->shouldCollect('memory', true)) {
            $this->addCollector(new MemoryCollector());
        }

        if ($this->shouldCollect('exceptions', true)) {
            try {
                $exceptionCollector = new ExceptionsCollector();
                $exceptionCollector->setChainExceptions(
                    $this->app['config']->get('debugbar.options.exceptions.chain', true)
                );
                $this->addCollector($exceptionCollector);
            } catch (\Exception $e) {
            }
        }

        if ($this->shouldCollect('laravel', false)) {
            $this->addCollector(new LaravelCollector($this->app));
        }

        if ($this->shouldCollect('default_request', false)) {
            $this->addCollector(new RequestDataCollector());
        }

        if ($this->shouldCollect('events', false) && isset($this->app['events'])) {
            try {
                $startTime = $this->app['request']->server('REQUEST_TIME_FLOAT');
                $eventCollector = new EventCollector($startTime);
                $this->addCollector($eventCollector);
                $this->app['events']->subscribe($eventCollector);

            } catch (\Exception $e) {
                $this->addThrowable(
                    new Exception(
                        'Cannot add EventCollector to Laravel Debugbar: ' . $e->getMessage(),
                        $e->getCode(),
                        $e
                    )
                );
            }
        }

        if ($this->shouldCollect('views', true) && isset($this->app['events'])) {
            try {
                $collectData = $this->app['config']->get('debugbar.options.views.data', true);
                $this->addCollector(new ViewCollector($collectData));
                $this->app['events']->listen(
                    'composing:*',
                    function ($view, $data = []) use ($debugbar) {
                        if ($data) {
                            $view = $data[0]; // For Laravel >= 5.4
                        }
                        $debugbar['views']->addView($view);
                    }
                );
            } catch (\Exception $e) {
                $this->addThrowable(
                    new Exception(
                        'Cannot add ViewCollector to Laravel Debugbar: ' . $e->getMessage(), $e->getCode(), $e
                    )
                );
            }
        }

        if (!$this->isLumen() && $this->shouldCollect('route')) {
            try {
                $this->addCollector($this->app->make('Barryvdh\Debugbar\DataCollector\RouteCollector'));
            } catch (\Exception $e) {
                $this->addThrowable(
                    new Exception(
                        'Cannot add RouteCollector to Laravel Debugbar: ' . $e->getMessage(),
                        $e->getCode(),
                        $e
                    )
                );
            }
        }

        if (!$this->isLumen() && $this->shouldCollect('log', true)) {
            try {
                if ($this->hasCollector('messages')) {
                    $logger = new MessagesCollector('log');
                    $this['messages']->aggregate($logger);
                    $this->app['log']->listen(
                        function ($level, $message = null, $context = null) use ($logger) {
                            // Laravel 5.4 changed how the global log listeners are called. We must account for
                            // the first argument being an "event object", where arguments are passed
                            // via object properties, instead of individual arguments.
                            if ($level instanceof \Illuminate\Log\Events\MessageLogged) {
                                $message = $level->message;
                                $context = $level->context;
                                $level = $level->level;
                            }

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
                $this->addThrowable(
                    new Exception(
                        'Cannot add LogsCollector to Laravel Debugbar: ' . $e->getMessage(), $e->getCode(), $e
                    )
                );
            }
        }

        if ($this->shouldCollect('db', true) && isset($this->app['db'])) {
            $db = $this->app['db'];
            if ($debugbar->hasCollector('time') && $this->app['config']->get(
                    'debugbar.options.db.timeline',
                    false
                )
            ) {
                $timeCollector = $debugbar->getCollector('time');
            } else {
                $timeCollector = null;
            }
            $queryCollector = new QueryCollector($timeCollector);

            $queryCollector->setDataFormatter(new QueryFormatter());

            if ($this->app['config']->get('debugbar.options.db.with_params')) {
                $queryCollector->setRenderSqlWithParams(true);
            }

            if ($this->app['config']->get('debugbar.options.db.backtrace')) {
                $middleware = ! $this->is_lumen ? $this->app['router']->getMiddleware() : [];
                $queryCollector->setFindSource(true, $middleware);
            }

            if ($this->app['config']->get('debugbar.options.db.explain.enabled')) {
                $types = $this->app['config']->get('debugbar.options.db.explain.types');
                $queryCollector->setExplainSource(true, $types);
            }

            if ($this->app['config']->get('debugbar.options.db.hints', true)) {
                $queryCollector->setShowHints(true);
            }

            $this->addCollector($queryCollector);

            try {
                $db->listen(
                    function ($query, $bindings = null, $time = null, $connectionName = null) use ($db, $queryCollector) {
                        if (!$this->shouldCollect('db', true)) {
                            return; // Issue 776 : We've turned off collecting after the listener was attached
                        }
                        // Laravel 5.2 changed the way some core events worked. We must account for
                        // the first argument being an "event object", where arguments are passed
                        // via object properties, instead of individual arguments.
                        if ( $query instanceof \Illuminate\Database\Events\QueryExecuted ) {
                            $bindings = $query->bindings;
                            $time = $query->time;
                            $connection = $query->connection;

                            $query = $query->sql;
                        } else {
                            $connection = $db->connection($connectionName);
                        }

                        $queryCollector->addQuery((string) $query, $bindings, $time, $connection);
                    }
                );
            } catch (\Exception $e) {
                $this->addThrowable(
                    new Exception(
                        'Cannot add listen to Queries for Laravel Debugbar: ' . $e->getMessage(),
                        $e->getCode(),
                        $e
                    )
                );
            }

            try {
                $db->getEventDispatcher()->listen([
                    \Illuminate\Database\Events\TransactionBeginning::class,
                    'connection.*.beganTransaction',
                ], function ($transaction) use ($queryCollector) {

                    // Laravel 5.2 changed the way some core events worked. We must account for
                    // the first argument being an "event object", where arguments are passed
                    // via object properties, instead of individual arguments.
                    if($transaction instanceof \Illuminate\Database\Events\TransactionBeginning) {
                        $connection = $transaction->connection;
                    } else {
                        $connection = $transaction;
                    }

                    $queryCollector->collectTransactionEvent('Begin Transaction', $connection);
                });

                $db->getEventDispatcher()->listen([
                    \Illuminate\Database\Events\TransactionCommitted::class,
                    'connection.*.committed',
                ], function ($transaction) use ($queryCollector) {

                    if($transaction instanceof \Illuminate\Database\Events\TransactionCommitted) {
                        $connection = $transaction->connection;
                    } else {
                        $connection = $transaction;
                    }

                    $queryCollector->collectTransactionEvent('Commit Transaction', $connection);
                });

                $db->getEventDispatcher()->listen([
                    \Illuminate\Database\Events\TransactionRolledBack::class,
                    'connection.*.rollingBack',
                ], function ($transaction) use ($queryCollector) {

                    if($transaction instanceof \Illuminate\Database\Events\TransactionRolledBack) {
                        $connection = $transaction->connection;
                    } else {
                        $connection = $transaction;
                    }

                    $queryCollector->collectTransactionEvent('Rollback Transaction', $connection);
                });
            } catch (\Exception $e) {
                $this->addThrowable(
                    new Exception(
                        'Cannot add listen transactions to Queries for Laravel Debugbar: ' . $e->getMessage(),
                        $e->getCode(),
                        $e
                    )
                );
            }
        }

        if ($this->shouldCollect('models', false)) {
            try {
                $modelsCollector = $this->app->make('Barryvdh\Debugbar\DataCollector\ModelsCollector');
                $this->addCollector($modelsCollector);
            } catch (\Exception $e){
                // No Models collector
            }
        }

        if ($this->shouldCollect('mail', true) && class_exists('Illuminate\Mail\MailServiceProvider')) {
            try {
                $mailer = $this->app['mailer']->getSwiftMailer();
                $this->addCollector(new SwiftMailCollector($mailer));
                if ($this->app['config']->get('debugbar.options.mail.full_log') && $this->hasCollector(
                        'messages'
                    )
                ) {
                    $this['messages']->aggregate(new SwiftLogCollector($mailer));
                }
            } catch (\Exception $e) {
                $this->addThrowable(
                    new Exception(
                        'Cannot add MailCollector to Laravel Debugbar: ' . $e->getMessage(), $e->getCode(), $e
                    )
                );
            }
        }

        if ($this->shouldCollect('logs', false)) {
            try {
                $file = $this->app['config']->get('debugbar.options.logs.file');
                $this->addCollector(new LogsCollector($file));
            } catch (\Exception $e) {
                $this->addThrowable(
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
                 $guards = $this->app['config']->get('auth.guards', []);
                 $authCollector = new MultiAuthCollector($app['auth'], $guards);

                 $authCollector->setShowName(
                     $this->app['config']->get('debugbar.options.auth.show_name')
                 );
                 $this->addCollector($authCollector);
             } catch (\Exception $e) {
                 $this->addThrowable(
                     new Exception(
                         'Cannot add AuthCollector to Laravel Debugbar: ' . $e->getMessage(), $e->getCode(), $e
                     )
                 );
             }
         }

        if ($this->shouldCollect('gate', false)) {
            try {
                $gateCollector = $this->app->make('Barryvdh\Debugbar\DataCollector\GateCollector');
                $this->addCollector($gateCollector);
            } catch (\Exception $e){
                // No Gate collector
            }
        }

        if ($this->shouldCollect('cache', false) && isset($this->app['events'])) {
            try {
                $collectValues = $this->app['config']->get('debugbar.options.cache.values', true);
                $startTime = $this->app['request']->server('REQUEST_TIME_FLOAT');
                $cacheCollector = new CacheCollector($startTime, $collectValues);
                $this->addCollector($cacheCollector);
                $this->app['events']->subscribe($cacheCollector);

            } catch (\Exception $e) {
                $this->addThrowable(
                    new Exception(
                        'Cannot add CacheCollector to Laravel Debugbar: ' . $e->getMessage(),
                        $e->getCode(),
                        $e
                    )
                );
            }
        }

        $renderer = $this->getJavascriptRenderer();
        $renderer->setIncludeVendors($this->app['config']->get('debugbar.include_vendors', true));
        $renderer->setBindAjaxHandlerToFetch($app['config']->get('debugbar.capture_ajax', true));
        $renderer->setBindAjaxHandlerToXHR($app['config']->get('debugbar.capture_ajax', true));

        $this->booted = true;
    }

    public function shouldCollect($name, $default = false)
    {
        return $this->app['config']->get('debugbar.collectors.' . $name, $default);
    }

    /**
     * Adds a data collector
     *
     * @param DataCollectorInterface $collector
     *
     * @throws DebugBarException
     * @return $this
     */
    public function addCollector(DataCollectorInterface $collector)
    {
        parent::addCollector($collector);

        if (method_exists($collector, 'useHtmlVarDumper')) {
            $collector->useHtmlVarDumper();
        }

        return $this;
    }

    /**
     * Handle silenced errors
     *
     * @param $level
     * @param $message
     * @param string $file
     * @param int $line
     * @param array $context
     * @throws \ErrorException
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if (error_reporting() & $level) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        } else {
            $this->addMessage($message, 'deprecation');
        }
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
                //  $this->addThrowable($e);
            }
        }
    }

    /**
     * Adds an exception to be profiled in the debug bar
     *
     * @param Exception $e
     * @deprecated in favor of addThrowable
     */
    public function addException(Exception $e)
    {
        return $this->addThrowable($e);
    }

    /**
     * Adds an exception to be profiled in the debug bar
     *
     * @param Exception $e
     */
    public function addThrowable($e)
    {
        if ($this->hasCollector('exceptions')) {
            /** @var \DebugBar\DataCollector\ExceptionsCollector $collector */
            $collector = $this->getCollector('exceptions');
            $collector->addThrowable($e);
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
        }
        return $this->jsRenderer;
    }

    /**
     * Modify the response and inject the debugbar (or data in headers)
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @param  \Symfony\Component\HttpFoundation\Response $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function modifyResponse(Request $request, Response $response)
    {
        $app = $this->app;
        if (!$this->isEnabled() || $this->isDebugbarRequest()) {
            return $response;
        }

        // Show the Http Response Exception in the Debugbar, when available
        if (isset($response->exception)) {
            $this->addThrowable($response->exception);
        }

        if ($this->shouldCollect('config', false)) {
            try {
                $configCollector = new ConfigCollector();
                $configCollector->setData($app['config']->all());
                $this->addCollector($configCollector);
            } catch (\Exception $e) {
                $this->addThrowable(
                    new Exception(
                        'Cannot add ConfigCollector to Laravel Debugbar: ' . $e->getMessage(),
                        $e->getCode(),
                        $e
                    )
                );
            }
        }

        if ($this->app->bound(SessionManager::class)){

            /** @var \Illuminate\Session\SessionManager $sessionManager */
            $sessionManager = $app->make(SessionManager::class);
            $httpDriver = new SymfonyHttpDriver($sessionManager, $response);
            $this->setHttpDriver($httpDriver);

            if ($this->shouldCollect('session') && ! $this->hasCollector('session')) {
                try {
                    $this->addCollector(new SessionCollector($sessionManager));
                } catch (\Exception $e) {
                    $this->addThrowable(
                        new Exception(
                            'Cannot add SessionCollector to Laravel Debugbar: ' . $e->getMessage(),
                            $e->getCode(),
                            $e
                        )
                    );
                }
            }
        } else {
            $sessionManager = null;
        }

        if ($this->shouldCollect('symfony_request', true) && !$this->hasCollector('request')) {
            try {
                $this->addCollector(new RequestCollector($request, $response, $sessionManager, $this->getCurrentRequestId()));
            } catch (\Exception $e) {
                $this->addThrowable(
                    new Exception(
                        'Cannot add SymfonyRequestCollector to Laravel Debugbar: ' . $e->getMessage(),
                        $e->getCode(),
                        $e
                    )
                );
            }
        }

        if ($app['config']->get('debugbar.clockwork') && ! $this->hasCollector('clockwork')) {

            try {
                $this->addCollector(new ClockworkCollector($request, $response, $sessionManager));
            } catch (\Exception $e) {
                $this->addThrowable(
                    new Exception(
                        'Cannot add ClockworkCollector to Laravel Debugbar: ' . $e->getMessage(),
                        $e->getCode(),
                        $e
                    )
                );
            }

            $this->addClockworkHeaders($response);
        }

        if ($response->isRedirection()) {
            try {
                $this->stackData();
            } catch (\Exception $e) {
                $app['log']->error('Debugbar exception: ' . $e->getMessage());
            }
        } elseif (
            $this->isJsonRequest($request) &&
            $app['config']->get('debugbar.capture_ajax', true)
        ) {
            try {
                $this->sendDataInHeaders(true);

                if ($app['config']->get('debugbar.add_ajax_timing', false)) {
                    $this->addServerTimingHeaders($response);
                }

            } catch (\Exception $e) {
                $app['log']->error('Debugbar exception: ' . $e->getMessage());
            }
        } elseif (
            ($response->headers->has('Content-Type') &&
                strpos($response->headers->get('Content-Type'), 'html') === false)
            || $request->getRequestFormat() !== 'html'
            || $response->getContent() === false
            || $this->isJsonRequest($request)
        ) {
            try {
                // Just collect + store data, don't inject it.
                $this->collect();
            } catch (\Exception $e) {
                $app['log']->error('Debugbar exception: ' . $e->getMessage());
            }
        } elseif ($app['config']->get('debugbar.inject', true)) {
            try {
                $this->injectDebugbar($response);
            } catch (\Exception $e) {
                $app['log']->error('Debugbar exception: ' . $e->getMessage());
            }
        }



        return $response;
    }

    /**
     * Check if the Debugbar is enabled
     * @return boolean
     */
    public function isEnabled()
    {
        if ($this->enabled === null) {
            $config = $this->app['config'];
            $configEnabled = value($config->get('debugbar.enabled'));

            if ($configEnabled === null) {
                $configEnabled = $config->get('app.debug');
            }

            $this->enabled = $configEnabled && !$this->app->runningInConsole() && !$this->app->environment('testing');
        }

        return $this->enabled;
    }

    /**
     * Check if this is a request to the Debugbar OpenHandler
     *
     * @return bool
     */
    protected function isDebugbarRequest()
    {
        return $this->app['request']->segment(1) == $this->app['config']->get('debugbar.route_prefix');
    }

    /**
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return bool
     */
    protected function isJsonRequest(Request $request)
    {
        // If XmlHttpRequest, return true
        if ($request->isXmlHttpRequest()) {
            return true;
        }

        // Check if the request wants Json
        $acceptable = $request->getAcceptableContentTypes();
        return (isset($acceptable[0]) && $acceptable[0] == 'application/json');
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

        $this->data = [
            '__meta' => [
                'id' => $this->getCurrentRequestId(),
                'datetime' => date('Y-m-d H:i:s'),
                'utime' => microtime(true),
                'method' => $request->getMethod(),
                'uri' => $request->getRequestUri(),
                'ip' => $request->getClientIp()
            ]
        ];

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
            $openHandlerUrl = route('debugbar.openhandler');
            $renderer->setOpenHandlerUrl($openHandlerUrl);
        }

        $renderedContent = $renderer->renderHead() . $renderer->render();

        $pos = strripos($content, '</body>');
        if (false !== $pos) {
            $content = substr($content, 0, $pos) . $renderedContent . substr($content, $pos);
        } else {
            $content = $content . $renderedContent;
        }

        // Update the new content and reset the content length
        $response->setContent($content);
        $response->headers->remove('Content-Length');
    }

    /**
     * Disable the Debugbar
     */
    public function disable()
    {
        $this->enabled = false;
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

        $this->data = [
            '__meta' => [
                'id' => $this->getCurrentRequestId(),
                'datetime' => date('Y-m-d H:i:s'),
                'utime' => microtime(true),
                'method' => 'CLI',
                'uri' => isset($_SERVER['argv']) ? implode(' ', $_SERVER['argv']) : null,
                'ip' => isset($_SERVER['SSH_CLIENT']) ? $_SERVER['SSH_CLIENT'] : null
            ]
        ];

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
        $messageLevels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug', 'log'];
        if (in_array($method, $messageLevels)) {
            foreach($args as $arg) {
                $this->addMessage($arg, $method);
            }
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

    protected function isLumen()
    {
        return $this->is_lumen;
    }

    /**
     * @param DebugBar $debugbar
     */
    protected function selectStorage(DebugBar $debugbar)
    {
        $config = $this->app['config'];
        if ($config->get('debugbar.storage.enabled')) {
            $driver = $config->get('debugbar.storage.driver', 'file');

            switch ($driver) {
                case 'pdo':
                    $connection = $config->get('debugbar.storage.connection');
                    $table = $this->app['db']->getTablePrefix() . 'phpdebugbar';
                    $pdo = $this->app['db']->connection($connection)->getPdo();
                    $storage = new PdoStorage($pdo, $table);
                    break;
                case 'redis':
                    $connection = $config->get('debugbar.storage.connection');
                    $client = $this->app['redis']->connection($connection);
                    if (is_a($client, 'Illuminate\Redis\Connections\Connection', false)) {
                        $client = $client->client();
                    }
                    $storage = new RedisStorage($client);
                    break;
                case 'custom':
                    $class = $config->get('debugbar.storage.provider');
                    $storage = $this->app->make($class);
                    break;
                case 'file':
                default:
                    $path = $config->get('debugbar.storage.path');
                    $storage = new FilesystemStorage($this->app['files'], $path);
                    break;
            }

            $debugbar->setStorage($storage);
        }
    }

    protected function addClockworkHeaders(Response $response)
    {
        $prefix = $this->app['config']->get('debugbar.route_prefix');
        $response->headers->set('X-Clockwork-Id', $this->getCurrentRequestId(), true);
        $response->headers->set('X-Clockwork-Version', 1, true);
        $response->headers->set('X-Clockwork-Path', $prefix .'/clockwork/', true);
    }

    /**
     * Add Server-Timing headers for the TimeData collector
     *
     * @see https://www.w3.org/TR/server-timing/
     * @param Response $response
     */
    protected function addServerTimingHeaders(Response $response)
    {
        if ($this->hasCollector('time')) {
            $collector = $this->getCollector('time');

            $headers = [];
            foreach ($collector->collect()['measures'] as $k => $m) {
                $headers[] = sprintf('%d=%F; "%s"', $k, $m['duration'] * 1000, str_replace('"', "'", $m['label']));
            }

            $response->headers->set('Server-Timing', $headers, false);
        }
    }
}
