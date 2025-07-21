<?php

namespace Barryvdh\Debugbar;

use Barryvdh\Debugbar\DataCollector\CacheCollector;
use Barryvdh\Debugbar\DataCollector\EventCollector;
use Barryvdh\Debugbar\DataCollector\FilesCollector;
use Barryvdh\Debugbar\DataCollector\GateCollector;
use Barryvdh\Debugbar\DataCollector\LaravelCollector;
use Barryvdh\Debugbar\DataCollector\LivewireCollector;
use Barryvdh\Debugbar\DataCollector\LogsCollector;
use Barryvdh\Debugbar\DataCollector\MultiAuthCollector;
use Barryvdh\Debugbar\DataCollector\PennantCollector;
use Barryvdh\Debugbar\DataCollector\QueryCollector;
use Barryvdh\Debugbar\DataCollector\SessionCollector;
use Barryvdh\Debugbar\DataCollector\RequestCollector;
use Barryvdh\Debugbar\DataCollector\RouteCollector;
use Barryvdh\Debugbar\DataCollector\ViewCollector;
use Barryvdh\Debugbar\DataFormatter\QueryFormatter;
use Barryvdh\Debugbar\Storage\SocketStorage;
use Barryvdh\Debugbar\Storage\FilesystemStorage;
use Barryvdh\Debugbar\Support\Clockwork\ClockworkCollector;
use Barryvdh\Debugbar\Support\RequestIdGenerator;
use DebugBar\Bridge\MonologCollector;
use DebugBar\Bridge\Symfony\SymfonyMailCollector;
use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\ObjectCountCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DebugBar;
use DebugBar\HttpDriverInterface;
use DebugBar\PhpHttpDriver;
use DebugBar\Storage\PdoStorage;
use DebugBar\Storage\RedisStorage;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\RawMessage;
use Throwable;

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
     * True when enabled, false disabled on null for still unknown
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
     * Laravel default error handler
     *
     * @var callable|null
     */
    protected $prevErrorHandler = null;

    protected ?string $editorTemplateLink = null;
    protected array $remoteServerReplacements = [];
    protected bool $responseIsModified = false;
    protected array $stackedData = [];
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
        if ($this->is_lumen) {
            $this->version = Str::betweenFirst($app->version(), '(', ')');
        } else {
            $this->setRequestIdGenerator(new RequestIdGenerator());
        }
    }

    /**
     * Returns the HTTP driver
     *
     * If no http driver where defined, a PhpHttpDriver is automatically created
     *
     * @return HttpDriverInterface
     */
    public function getHttpDriver()
    {
        if ($this->httpDriver === null) {
            $this->httpDriver = $this->app->make(SymfonyHttpDriver::class);
        }

        return $this->httpDriver;
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

        /** @var Application $app */
        $app = $this->app;

        /** @var \Illuminate\Config\Repository $config */
        $config = $app['config'];

        /** @var \Illuminate\Events\Dispatcher|null $events */
        $events = isset($app['events']) ? $app['events'] : null;

        $this->editorTemplateLink = $config->get('debugbar.editor') ?: null;
        $this->remoteServerReplacements = $this->getRemoteServerReplacements();

        // Set custom error handler
        if ($config->get('debugbar.error_handler', false)) {
            $this->prevErrorHandler = set_error_handler([$this, 'handleError']);
        }

        $this->selectStorage($this);

        if ($this->shouldCollect('phpinfo', true)) {
            $this->addCollector(new PhpInfoCollector());
        }

        if ($this->shouldCollect('messages', true)) {
            $this->addCollector(new MessagesCollector());

            if ($config->get('debugbar.options.messages.trace', true)) {
                $this['messages']->collectFileTrace(true);
            }

            if ($config->get('debugbar.options.messages.capture_dumps', false)) {
                $originalHandler = \Symfony\Component\VarDumper\VarDumper::setHandler(function ($var) use (&$originalHandler) {
                    if ($originalHandler) {
                        $originalHandler($var);
                    }

                    self::addMessage($var);
                });
            }
        }

        if ($this->shouldCollect('time', true)) {
            $startTime = $app['request']->server('REQUEST_TIME_FLOAT');

            if (!$this->hasCollector('time')) {
                $this->addCollector(new TimeDataCollector($startTime));
            }

            if ($config->get('debugbar.options.time.memory_usage')) {
                $this['time']->showMemoryUsage();
            }

            if ($startTime && !$this->isLumen()) {
                $app->booted(
                    function () use ($startTime) {
                        $this->addMeasure('Booting', $startTime, microtime(true), [], 'time');
                    }
                );
            }

            $this->startMeasure('application', 'Application', 'time');

            if ($events) {
                 $events->listen(\Illuminate\Routing\Events\Routing::class, function() {
                     $this->startMeasure('Routing');
                 });
                 $events->listen(\Illuminate\Routing\Events\RouteMatched::class, function() {
                     $this->stopMeasure('Routing');
                 });

                $events->listen(\Illuminate\Routing\Events\PreparingResponse::class, function() {
                    $this->startMeasure('Preparing Response');
                });
                $events->listen(\Illuminate\Routing\Events\ResponsePrepared::class, function() {
                    $this->stopMeasure('Preparing Response');
                });
            }
        }

        if ($this->shouldCollect('memory', true)) {
            $this->addCollector(new MemoryCollector());
            $this['memory']->setPrecision($config->get('debugbar.options.memory.precision', 0));

            if (function_exists('memory_reset_peak_usage') && $config->get('debugbar.options.memory.reset_peak')) {
                memory_reset_peak_usage();
            }
            if ($config->get('debugbar.options.memory.with_baseline')) {
                $this['memory']->resetMemoryBaseline();
            }
        }

        if ($this->shouldCollect('exceptions', true)) {
            try {
                $this->addCollector(new ExceptionsCollector());
                $this['exceptions']->setChainExceptions(
                    $config->get('debugbar.options.exceptions.chain', true)
                );
            } catch (Exception $e) {
            }
        }

        if ($this->shouldCollect('laravel', false)) {
            $this->addCollector(new LaravelCollector($app));
        }

        if ($this->shouldCollect('default_request', false)) {
            $this->addCollector(new RequestDataCollector());
        }

        if ($this->shouldCollect('events', false) && $events) {
            try {
                $startTime = $app['request']->server('REQUEST_TIME_FLOAT');
                $collectData = $config->get('debugbar.options.events.data', false);
                $excludedEvents = $config->get('debugbar.options.events.excluded', []);
                $this->addCollector(new EventCollector($startTime, $collectData, $excludedEvents));
                $events->subscribe($this['event']);
            } catch (Exception $e) {
                $this->addCollectorException('Cannot add EventCollector', $e);
            }
        }

        if ($this->shouldCollect('views', true) && $events) {
            try {
                $collectData = $config->get('debugbar.options.views.data', true);
                $excludePaths = $config->get('debugbar.options.views.exclude_paths', []);
                $group = $config->get('debugbar.options.views.group', true);
                if ($this->hasCollector('time') && $config->get('debugbar.options.views.timeline', false)) {
                    $timeCollector = $this['time'];
                } else {
                    $timeCollector = null;
                }
                $this->addCollector(new ViewCollector($collectData, $excludePaths, $group, $timeCollector));
                $events->listen(
                    'composing:*',
                    function ($event, $params) {
                        $this['views']->addView($params[0]);
                    }
                );
            } catch (Exception $e) {
                $this->addCollectorException('Cannot add ViewCollector', $e);
            }
        }

        if (!$this->isLumen() && $this->shouldCollect('route')) {
            try {
                $this->addCollector($app->make(RouteCollector::class));
            } catch (Exception $e) {
                $this->addCollectorException('Cannot add RouteCollector', $e);
            }
        }

        if (!$this->isLumen() && $this->shouldCollect('log', true)) {
            try {
                if ($this->hasCollector('messages')) {
                    $logger = new MessagesCollector('log');
                    $this['messages']->aggregate($logger);
                    $app['log']->listen(
                        function (\Illuminate\Log\Events\MessageLogged $log) use ($logger) {
                            try {
                                $logMessage = (string) $log->message;
                                if (mb_check_encoding($logMessage, 'UTF-8')) {
                                    $context = $log->context;
                                    $logMessage .= (!empty($context) ? ' ' . json_encode($context, JSON_PRETTY_PRINT) : '');
                                } else {
                                    $logMessage = "[INVALID UTF-8 DATA]";
                                }
                            } catch (Exception $e) {
                                $logMessage = "[Exception: " . $e->getMessage() . "]";
                            }
                            $logger->addMessage(
                                '[' . date('H:i:s') . '] ' . "LOG.{$log->level}: " . $logMessage,
                                $log->level,
                                false
                            );
                        }
                    );
                } else {
                    $this->addCollector(new MonologCollector($this->getMonologLogger()));
                }
            } catch (Exception $e) {
                $this->addCollectorException('Cannot add LogsCollector', $e);
            }
        }

        if ($this->shouldCollect('db', true) && isset($app['db']) && $events) {
            if ($this->hasCollector('time') && $config->get('debugbar.options.db.timeline', false)) {
                $timeCollector = $this['time'];
            } else {
                $timeCollector = null;
            }
            $queryCollector = new QueryCollector($timeCollector);

            $queryCollector->setDataFormatter(new QueryFormatter());
            $queryCollector->setLimits($config->get('debugbar.options.db.soft_limit'), $config->get('debugbar.options.db.hard_limit'));
            $queryCollector->setDurationBackground($config->get('debugbar.options.db.duration_background'));

            $threshold = $config->get('debugbar.options.db.slow_threshold', false);
            if ($threshold && !$config->get('debugbar.options.db.only_slow_queries', true)) {
                $queryCollector->setSlowThreshold($threshold);
            }

            if ($config->get('debugbar.options.db.with_params')) {
                $queryCollector->setRenderSqlWithParams(true);
            }

            if ($dbBacktrace = $config->get('debugbar.options.db.backtrace')) {
                $middleware = ! $this->is_lumen ? $app['router']->getMiddleware() : [];
                $queryCollector->setFindSource($dbBacktrace, $middleware);
            }

            if ($excludePaths = $config->get('debugbar.options.db.exclude_paths')) {
                $queryCollector->mergeExcludePaths($excludePaths);
            }

            if ($excludeBacktracePaths = $config->get('debugbar.options.db.backtrace_exclude_paths')) {
                $queryCollector->mergeBacktraceExcludePaths($excludeBacktracePaths);
            }

            if ($config->get('debugbar.options.db.explain.enabled')) {
                $types = $config->get('debugbar.options.db.explain.types');
                $queryCollector->setExplainSource(true, $types);
            }

            if ($config->get('debugbar.options.db.hints', true)) {
                $queryCollector->setShowHints(true);
            }

            if ($config->get('debugbar.options.db.show_copy', false)) {
                $queryCollector->setShowCopyButton(true);
            }

            $this->addCollector($queryCollector);

            try {
                $events->listen(
                    function (\Illuminate\Database\Events\QueryExecuted $query) {
                        if (!app(static::class)->shouldCollect('db', true)) {
                            return; // Issue 776 : We've turned off collecting after the listener was attached
                        }

                        $threshold = app('config')->get('debugbar.options.db.slow_threshold', false);
                        $onlyThreshold = app('config')->get('debugbar.options.db.only_slow_queries', true);
                        //allow collecting only queries slower than a specified amount of milliseconds
                        if (!$onlyThreshold || !$threshold || $query->time > $threshold) {
                            $this['queries']->addQuery($query);
                        }
                    }
                );
            } catch (Exception $e) {
                $this->addCollectorException('Cannot listen to Queries', $e);
            }

            try {
                $events->listen(
                    \Illuminate\Database\Events\TransactionBeginning::class,
                    function ($transaction) {
                        $this['queries']->collectTransactionEvent('Begin Transaction', $transaction->connection);
                    }
                );

                $events->listen(
                    \Illuminate\Database\Events\TransactionCommitted::class,
                    function ($transaction) {
                        $this['queries']->collectTransactionEvent('Commit Transaction', $transaction->connection);
                    }
                );

                $events->listen(
                    \Illuminate\Database\Events\TransactionRolledBack::class,
                    function ($transaction) {
                        $this['queries']->collectTransactionEvent('Rollback Transaction', $transaction->connection);
                    }
                );

                $events->listen(
                    'connection.*.beganTransaction',
                    function ($event, $params) {
                        $this['queries']->collectTransactionEvent('Begin Transaction', $params[0]);
                    }
                );

                $events->listen(
                    'connection.*.committed',
                    function ($event, $params) {
                        $this['queries']->collectTransactionEvent('Commit Transaction', $params[0]);
                    }
                );

                $events->listen(
                    'connection.*.rollingBack',
                    function ($event, $params) {
                        $this['queries']->collectTransactionEvent('Rollback Transaction', $params[0]);
                    }
                );

                $events->listen(
                    function (\Illuminate\Database\Events\ConnectionEstablished $event) {
                        $this['queries']->collectTransactionEvent('Connection Established', $event->connection);

                        if (app('config')->get('debugbar.options.db.memory_usage')) {
                            $event->connection->beforeExecuting(function () {
                                $this['queries']->startMemoryUsage();
                            });
                        }
                    }
                );
            } catch (Exception $e) {
                $this->addCollectorException('Cannot listen transactions to Queries', $e);
            }
        }

        if ($this->shouldCollect('models', true) && $events) {
            try {
                $this->addCollector(new ObjectCountCollector('models'));
                $eventList = ['retrieved', 'created', 'updated', 'deleted'];
                $this['models']->setKeyMap(array_combine($eventList, array_map('ucfirst', $eventList)));
                $this['models']->collectCountSummary(true);
                foreach ($eventList as $event) {
                    $events->listen("eloquent.{$event}: *", function ($event, $models) {
                        $event = explode(': ', $event);
                        $count = count(array_filter($models));
                        $this['models']->countClass($event[1], $count, explode('.', $event[0])[1]);
                    });
                }
            } catch (Exception $e) {
                $this->addCollectorException('Cannot add Models Collector', $e);
            }
        }

        if ($this->shouldCollect('livewire', true) && $app->bound('livewire')) {
            try {
                $this->addCollector($app->make(LivewireCollector::class));
            } catch (Exception $e) {
                $this->addCollectorException('Cannot add Livewire Collector', $e);
            }
        }

        if ($this->shouldCollect('mail', true) && class_exists('Illuminate\Mail\MailServiceProvider') && $events) {
            try {
                $mailCollector = new SymfonyMailCollector();
                $this->addCollector($mailCollector);
                $events->listen(function (MessageSent $event) use ($mailCollector) {
                    $mailCollector->addSymfonyMessage($event->sent->getSymfonySentMessage());
                });

                if ($config->get('debugbar.options.mail.show_body') || $config->get('debugbar.options.mail.full_log')) {
                    $mailCollector->showMessageBody();
                }

                if ($this->hasCollector('time') && $config->get('debugbar.options.mail.timeline')) {
                    $transport = $app['mailer']->getSymfonyTransport();
                    $app['mailer']->setSymfonyTransport(new class ($transport, $this) extends AbstractTransport{
                        private $originalTransport;
                        private $laravelDebugbar;

                        public function __construct($transport, $laravelDebugbar)
                        {
                            $this->originalTransport = $transport;
                            $this->laravelDebugbar = $laravelDebugbar;
                        }
                        public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
                        {
                            return $this->laravelDebugbar['time']->measure(
                                'mail: ' . Str::limit($message->getSubject(), 100),
                                function () use ($message, $envelope) {
                                    return $this->originalTransport->send($message, $envelope);
                                },
                                'mail'
                            );
                        }
                        protected function doSend(SentMessage $message): void
                        {
                        }
                        public function __toString(): string
                        {
                            return $this->originalTransport->__toString();
                        }
                    });
                }
            } catch (Exception $e) {
                $this->addCollectorException('Cannot add SymfonyMailCollector', $e);
            }
        }

        if ($this->shouldCollect('logs', false)) {
            try {
                $file = $config->get('debugbar.options.logs.file');
                $this->addCollector(new LogsCollector($file));
            } catch (Exception $e) {
                $this->addCollectorException('Cannot add LogsCollector', $e);
            }
        }
        if ($this->shouldCollect('files', false)) {
            $this->addCollector(new FilesCollector($app));
        }

        if ($this->shouldCollect('auth', false)) {
            try {
                $guards = $config->get('auth.guards', []);
                $this->addCollector(new MultiAuthCollector($app['auth'], $guards));

                $this['auth']->setShowName(
                    $config->get('debugbar.options.auth.show_name')
                );
                $this['auth']->setShowGuardsData(
                    $config->get('debugbar.options.auth.show_guards', true)
                );
            } catch (Exception $e) {
                $this->addCollectorException('Cannot add AuthCollector', $e);
            }
        }

        if ($this->shouldCollect('gate', false)) {
            try {
                $this->addCollector($app->make(GateCollector::class));

                if ($config->get('debugbar.options.gate.trace', false)) {
                    $this['gate']->collectFileTrace(true);
                    $this['gate']->addBacktraceExcludePaths($config->get('debugbar.options.gate.exclude_paths',[]));
                }
            } catch (Exception $e) {
                $this->addCollectorException('Cannot add GateCollector', $e);
            }
        }

        if ($this->shouldCollect('cache', false) && $events) {
            try {
                $collectValues = $config->get('debugbar.options.cache.values', true);
                $startTime = $app['request']->server('REQUEST_TIME_FLOAT');
                $this->addCollector(new CacheCollector($startTime, $collectValues));
                $events->subscribe($this['cache']);
            } catch (Exception $e) {
                $this->addCollectorException('Cannot add CacheCollector', $e);
            }
        }

        if ($this->shouldCollect('jobs', false) && $events) {
            try {
                $this->addCollector(new ObjectCountCollector('jobs', 'briefcase'));
                $events->listen(\Illuminate\Queue\Events\JobQueued::class, function ($event) {
                    $this['jobs']->countClass($event->job);
                });
            } catch (Exception $e) {
                $this->addCollectorException('Cannot add Jobs Collector', $e);
            }
        }

        if ($this->shouldCollect('pennant', false)) {
            if (class_exists('Laravel\Pennant\FeatureManager') && $app->bound(\Laravel\Pennant\FeatureManager::class)) {
                $featureManager = $app->make(\Laravel\Pennant\FeatureManager::class);
                try {
                    $this->addCollector(new PennantCollector($featureManager));
                } catch (Exception $e) {
                    $this->addCollectorException('Cannot add PennantCollector', $e);
                }
            }
        }

        $renderer = $this->getJavascriptRenderer();
        $renderer->setHideEmptyTabs($config->get('debugbar.hide_empty_tabs', false));
        $renderer->setIncludeVendors($config->get('debugbar.include_vendors', true));
        $renderer->setBindAjaxHandlerToFetch($config->get('debugbar.capture_ajax', true));
        $renderer->setBindAjaxHandlerToXHR($config->get('debugbar.capture_ajax', true));
        $renderer->setDeferDatasets($config->get('debugbar.defer_datasets', false));

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
        if (method_exists($collector, 'setEditorLinkTemplate') && $this->editorTemplateLink) {
            $collector->setEditorLinkTemplate($this->editorTemplateLink);
        }
        if (method_exists($collector, 'addXdebugReplacements') && $this->remoteServerReplacements) {
            $collector->addXdebugReplacements($this->remoteServerReplacements);
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
        if ($this->hasCollector('exceptions')) {
            $this['exceptions']->addWarning($level, $message, $file, $line);
        }

        if ($this->hasCollector('messages')) {
            $file = $file ? ' on ' . $this['messages']->normalizeFilePath($file) . ":{$line}" : '';
            $this['messages']->addMessage($message . $file, 'deprecation');
        }

        if (! $this->prevErrorHandler) {
            return;
        }

        return call_user_func($this->prevErrorHandler, $level, $message, $file, $line, $context);
    }

    /**
     * Starts a measure
     *
     * @param string $name Internal name, used to stop the measure
     * @param string $label Public name
     * @param string|null $collector
     * @param string|null $group
     */
    public function startMeasure($name, $label = null, $collector = null, $group = null)
    {
        if ($this->hasCollector('time')) {
            /** @var \DebugBar\DataCollector\TimeDataCollector */
            $time = $this->getCollector('time');
            $time->startMeasure($name, $label, $collector, $group);
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
            } catch (Exception $e) {
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
     * @param Throwable $e
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
     * Register collector exceptions
     *
     * @param string $message
     * @param Exception $exception
     */
    protected function addCollectorException(string $message, Exception $exception)
    {
        $this->addThrowable(
            new Exception(
                $message . ' on Laravel Debugbar: ' . $exception->getMessage(),
                (int) $exception->getCode(),
                $exception
            )
        );
    }

    /**
     * Returns a JavascriptRenderer for this instance
     *
     * @param string $baseUrl
     * @param string $basePath
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
        /** @var Application $app */
        $app = $this->app;
        if (!$this->isEnabled() || !$this->booted || $this->isDebugbarRequest() || $this->responseIsModified) {
            return $response;
        }

        // Prevent duplicate modification
        $this->responseIsModified = true;

        // Set the Response if required
        $httpDriver = $this->getHttpDriver();
        if ($httpDriver instanceof SymfonyHttpDriver) {
            $httpDriver->setResponse($response);
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
            } catch (Exception $e) {
                $this->addCollectorException('Cannot add ConfigCollector', $e);
            }
        }

        $sessionHiddens = $app['config']->get('debugbar.options.session.hiddens', []);
        if ($app->bound(SessionManager::class)) {
            /** @var \Illuminate\Session\SessionManager $sessionManager */
            $sessionManager = $app->make(SessionManager::class);

            if ($this->shouldCollect('session') && ! $this->hasCollector('session')) {
                try {
                    $this->addCollector(new SessionCollector($sessionManager, $sessionHiddens));
                } catch (Exception $e) {
                    $this->addCollectorException('Cannot add SessionCollector', $e);
                }
            }
        } else {
            $sessionManager = null;
        }

        $requestHiddens = array_merge(
            $app['config']->get('debugbar.options.symfony_request.hiddens', []),
            array_map(fn ($key) => 'session_attributes.' . $key, $sessionHiddens)
        );
        if ($this->shouldCollect('symfony_request', true) && !$this->hasCollector('request')) {
            try {
                $reqId = $this->getCurrentRequestId();
                $this->addCollector(new RequestCollector($request, $response, $sessionManager, $reqId, $requestHiddens));
            } catch (Exception $e) {
                $this->addCollectorException('Cannot add SymfonyRequestCollector', $e);
            }
        }

        if ($app['config']->get('debugbar.clockwork') && ! $this->hasCollector('clockwork')) {
            try {
                $this->addCollector(new ClockworkCollector($request, $response, $sessionManager, $requestHiddens));
            } catch (Exception $e) {
                $this->addCollectorException('Cannot add ClockworkCollector', $e);
            }

            $this->addClockworkHeaders($response);
        }

        try {
            if ($this->hasCollector('views') && $response->headers->has('X-Inertia')) {
                $content = $response->getContent();

                if (is_string($content)) {
                    $content = json_decode($content, true);
                }

                if (is_array($content)) {
                    $this['views']->addInertiaAjaxView($content);
                }
            }
        } catch (Exception $e) {
        }

        if ($app['config']->get('debugbar.add_ajax_timing', false)) {
            $this->addServerTimingHeaders($response);
        }

        if ($response->isRedirection()) {
            try {
                $this->stackData();
            } catch (Exception $e) {
                $app['log']->error('Debugbar exception: ' . $e->getMessage());
            }

            return $response;
        }

        try {
            // Collect + store data, only inject the ID in theheaders
            $this->sendDataInHeaders(true);
        } catch (Exception $e) {
            $app['log']->error('Debugbar exception: ' . $e->getMessage());
        }

        // Check if it's safe to inject the Debugbar
        if (
            $app['config']->get('debugbar.inject', true)
            && str_contains($response->headers->get('Content-Type', 'text/html'), 'html')
            && !$this->isJsonRequest($request, $response)
            && $response->getContent() !== false
            && in_array($request->getRequestFormat(), [null, 'html'], true)
        ) {
            try {
                $this->injectDebugbar($response);
            } catch (Exception $e) {
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
            /** @var \Illuminate\Config\Repository $config */
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
        return $this->app['request']->is($this->app['config']->get('debugbar.route_prefix') . '*');
    }

    /**
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @param  \Symfony\Component\HttpFoundation\Response $response
     * @return bool
     */
    protected function isJsonRequest(Request $request, Response $response)
    {
        // If XmlHttpRequest, Live or HTMX, return true
        if (
            $request->isXmlHttpRequest() ||
            $request->headers->has('X-Livewire') ||
            ($request->headers->has('Hx-Request') && $request->headers->has('Hx-Target'))
        ) {
            return true;
        }

        // Check if the request wants Json
        $acceptable = $request->getAcceptableContentTypes();
        if (isset($acceptable[0]) && in_array($acceptable[0], ['application/json', 'application/javascript'], true)) {
            return true;
        }

        // Check if content looks like JSON without actually validating
        $content = $response->getContent();
        if (is_string($content) && strlen($content) > 0 && in_array($content[0], ['{', '['], true)) {
            return true;
        }

        return false;
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
        /** @var \Illuminate\Config\Repository $config */
        $config = $this->app['config'];
        $content = $response->getContent();

        $renderer = $this->getJavascriptRenderer();
        $autoShow = $config->get('debugbar.ajax_handler_auto_show', true);
        $renderer->setAjaxHandlerAutoShow($autoShow);

        $enableTab = $config->get('debugbar.ajax_handler_enable_tab', true);
        $renderer->setAjaxHandlerEnableTab($enableTab);

        if ($this->getStorage()) {
            $openHandlerUrl = route('debugbar.openhandler');
            $renderer->setOpenHandlerUrl($openHandlerUrl);
        }

        $head = $renderer->renderHead();
        $widget = $renderer->render();

        // Try to put the js/css directly before the </head>
        $pos = stripos($content, '</head>');
        if (false !== $pos) {
            $content = substr($content, 0, $pos) . $head . substr($content, $pos);
        } else {
            // Append the head before the widget
            $widget = $head . $widget;
        }

        // Try to put the widget at the end, directly before the </body>
        $pos = strripos($content, '</body>');
        if (false !== $pos) {
            $content = substr($content, 0, $pos) . $widget . substr($content, $pos);
        } else {
            $content = $content . $widget;
        }

        $original = null;
        if ($response instanceof \Illuminate\Http\Response && $response->getOriginalContent()) {
            $original = $response->getOriginalContent();
        }

        // Update the new content and reset the content length
        $response->setContent($content);
        $response->headers->remove('Content-Length');

        // Restore original response (e.g. the View or Ajax data)
        if ($original) {
            $response->original = $original;
        }
    }

    /**
     * Checks if there is stacked data in the session
     *
     * @return boolean
     */
    public function hasStackedData()
    {
        return count($this->getStackedData(false)) > 0;
    }

    /**
     * Returns the data stacked in the session
     *
     * @param boolean $delete Whether to delete the data in the session
     * @return array
     */
    public function getStackedData($delete = true): array
    {
        $this->stackedData = array_merge($this->stackedData, parent::getStackedData($delete));

        return $this->stackedData;
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
     * @param array|null $params
     * @param string|null $collector
     * @param string|null $group
     */
    public function addMeasure($label, $start, $end, $params = [], $collector = null, $group = null)
    {
        if ($this->hasCollector('time')) {
            /** @var \DebugBar\DataCollector\TimeDataCollector */
            $time = $this->getCollector('time');
            $time->addMeasure($label, $start, $end, $params, $collector, $group);
        }
    }

    /**
     * Utility function to measure the execution of a Closure
     *
     * @param string $label
     * @param \Closure $closure
     * @param string|null $collector
     * @param string|null $group
     * @return mixed
     */
    public function measure($label, \Closure $closure, $collector = null, $group = null)
    {
        if ($this->hasCollector('time')) {
            /** @var \DebugBar\DataCollector\TimeDataCollector  */
            $time = $this->getCollector('time');
            $result = $time->measure($label, $closure, $collector, $group);
        } else {
            $result = $closure();
        }
        return $result;
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
            foreach ($args as $arg) {
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
        /** @var \Illuminate\Config\Repository $config */
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
                case 'socket':
                    $hostname = $config->get('debugbar.storage.hostname', '127.0.0.1');
                    $port = $config->get('debugbar.storage.port', 2304);
                    $storage = new SocketStorage($hostname, $port);
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
        $response->headers->set('X-Clockwork-Version', 9, true);
        $response->headers->set('X-Clockwork-Path', $prefix . '/clockwork/', true);
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
            foreach ($collector->collect()['measures'] as $m) {
                $headers[] = sprintf('app;desc="%s";dur=%F', str_replace(array("\n", "\r"), ' ', str_replace('"', "'", $m['label'])), $m['duration'] * 1000);
            }

            $response->headers->set('Server-Timing', $headers, false);
        }
    }

    /**
     * @return array
     */
    private function getRemoteServerReplacements()
    {
        $localPath = $this->app['config']->get('debugbar.local_sites_path') ?: base_path();
        $remotePaths = array_filter(explode(',', $this->app['config']->get('debugbar.remote_sites_path') ?: '')) ?: [base_path()];

        return array_fill_keys($remotePaths, $localPath);
    }

    /**
     * @return \Monolog\Logger
     * @throws Exception
     */
    private function getMonologLogger()
    {
        $logger = $this->app['log']->getLogger();

        if (get_class($logger) !== 'Monolog\Logger') {
            throw new Exception('Logger is not a Monolog\Logger instance');
        }

        return $logger;
    }
}
