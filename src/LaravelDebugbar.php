<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar;

use Barryvdh\Debugbar\CollectorProviders\ConfigCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\DefaultRequestCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\ExceptionsCollectorProvider;
use Barryvdh\Debugbar\DataCollector\LaravelCollector;
use Barryvdh\Debugbar\DataCollector\SessionCollector;
use Barryvdh\Debugbar\DataCollector\RequestCollector;
use Barryvdh\Debugbar\DataCollector\ViewCollector;
use Barryvdh\Debugbar\CollectorProviders\AuthCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\CacheCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\DatabaseCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\EventsCollectorCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\GateCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\JobsCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\LaravelCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\LivewireCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\LogCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\MailCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\MemoryCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\MessagesCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\ModelsCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\PennantCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\PhpInfoCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\RouteCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\TimeCollectorProvider;
use Barryvdh\Debugbar\CollectorProviders\ViewsCollectorProvider;
use Barryvdh\Debugbar\Storage\FilesystemStorage;
use Barryvdh\Debugbar\Support\Clockwork\ClockworkCollector;
use Barryvdh\Debugbar\Support\RequestIdGenerator;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DebugBar;
use DebugBar\HttpDriverInterface;
use DebugBar\PhpHttpDriver;
use DebugBar\Storage\PdoStorage;
use DebugBar\Storage\RedisStorage;
use Exception;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     */
    protected \Illuminate\Foundation\Application $app;

    /**
     * Normalized Laravel Version
     *
     */
    protected string $version;

    /**
     * True when booted.
     *
     */
    protected ?bool $booted = false;

    /**
     * True when enabled, false disabled on null for still unknown
     *
     */
    protected ?bool $enabled = null;

    /**
     * Laravel default error handler
     *
     * @var callable|null
     */
    protected $prevErrorHandler = null;

    protected ?string $editorTemplate = null;
    protected bool $responseIsModified = false;
    protected array $stackedData = [];

    public function __construct(?\Illuminate\Foundation\Application $app = null)
    {
        if (!$app) {
            /** @var \Illuminate\Foundation\Application $app */
            $app = app();   //Fallback when $app is not given
        }
        $this->app = $app;
        $this->version = $app->version();
        $this->setRequestIdGenerator(new RequestIdGenerator());
    }

    /**
     * Returns the HTTP driver
     *
     * If no http driver where defined, a PhpHttpDriver is automatically created
     *
     */
    public function getHttpDriver(): HttpDriverInterface
    {
        if ($this->httpDriver === null) {
            $this->httpDriver = $this->app->make(SymfonyHttpDriver::class);
        }

        return $this->httpDriver;
    }

    /**
     * Enable the Debugbar and boot, if not already booted.
     */
    public function enable(): void
    {
        $this->enabled = true;

        if (!$this->booted) {
            $this->boot();
        }
    }

    /**
     * Boot the debugbar (add collectors, renderer and listener)
     */
    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        /** @var Repository $config */
        $config = $this->app->get(Repository::class);

        $this->editorTemplate = $config->get('debugbar.editor') ?: null;
        $this->remotePathReplacements = $this->getRemoteServerReplacements();

        // Set custom error handler
        if ($config->get('debugbar.error_handler', false)) {
            // Get the error_level config, default to E_ALL
            $errorLevel = $config->get('debugbar.error_level', E_ALL);

            // set error handler with configured error reporting level
            $this->prevErrorHandler = set_error_handler([$this, 'handleError'], $errorLevel);
        }

        $this->selectStorage($this);
        $this->registerCollectors();

        $renderer = $this->getJavascriptRenderer();
        $renderer->setHideEmptyTabs($config->get('debugbar.hide_empty_tabs', false));
        $renderer->setIncludeVendors($config->get('debugbar.include_vendors', true));
        $renderer->setBindAjaxHandlerToFetch($config->get('debugbar.capture_ajax', true));
        $renderer->setBindAjaxHandlerToXHR($config->get('debugbar.capture_ajax', true));
        $renderer->setDeferDatasets($config->get('debugbar.defer_datasets', false));
        $renderer->setUseDistFiles($config->get('debugbar.use_dist_files', true));
        $this->booted = true;
    }

    protected function registerCollectors()
    {
        /** @var Repository $config */
        $config = $this->app->get(Repository::class);

        // Register default Collector Provider
        $this->registerCollectorProviders([
            'exceptions' => ExceptionsCollectorProvider::class,
            'phpinfo' => PhpInfoCollectorProvider::class,
            'messages' => MessagesCollectorProvider::class,
            'time' => TimeCollectorProvider::class,
            'memory' => MemoryCollectorProvider::class,
            'laravel' => LaravelCollectorProvider::class,
            'default_request' => DefaultRequestCollectorProvider::class,
            'events' => EventsCollectorCollectorProvider::class,
            'views' => ViewsCollectorProvider::class,
            'route' => RouteCollectorProvider::class,
            'log' => LogCollectorProvider::class,
            'db' => DatabaseCollectorProvider::class,
            'models' => ModelsCollectorProvider::class,
            'livewire' => LivewireCollectorProvider::class,
            'mail' => MailCollectorProvider::class,
            'auth' => AuthCollectorProvider::class,
            'gate' => GateCollectorProvider::class,
            'cache' => CacheCollectorProvider::class,
            'jobs' => JobsCollectorProvider::class,
            'pennant' => PennantCollectorProvider::class,
            'config' => ConfigCollectorProvider::class,
        ]);

        // Register any Custom Collectors
        $this->registerCustomCollectorProviders($config->get('debugbar.custom_collectors', []));
    }
    /**
     * @param array<string, string> $providers
     */
    protected function registerCollectorProviders(array $providers): void
    {
        /** @var Repository $config */
        $config = $this->app->get(Repository::class);
        foreach ($providers as $name => $provider) {
            if (!$this->shouldCollect($name)) {
                continue;
            }
            try {
                $options = $config->get('debugbar.options.' . $name, []);
                $this->app->call($provider, ['options' => $options]);
            } catch (Exception $e) {
                $this->addCollectorException('Error calling ' . class_basename($provider), $e);
            }
        }
    }

    /**
     * @param array<string, bool> $providers
     */
    protected function registerCustomCollectorProviders(array $providers): void
    {
        foreach ($providers as $provider => $enabled) {
            if (!$enabled) {
                continue;
            }
            try {
                $provider = $this->app->make($provider);
                // Add collectors directly, otherwise invoke the class
                if (is_a($provider, DataCollectorInterface::class)) {
                    $this->addCollector($provider);
                } else {
                    $this->app->call($provider);
                }
            } catch (Exception $e) {
                $this->addCollectorException('Error calling ' . class_basename($provider), $e);
            }
        }
    }

    public function shouldCollect(string $name, bool $default = true): bool
    {
        return $this->app['config']->get('debugbar.collectors.' . $name, $default);
    }

    /**
     * Handle silenced errors
     */
    public function handleError(int $level, string $message, string $file = '', int $line = 0, array $context = []): mixed
    {
        if ($this->hasCollector('exceptions')) {
            /** @var ExceptionsCollector $exceptionCollector */
            $exceptionCollector = $this['exceptions'];
            $exceptionCollector->addWarning($level, $message, $file, $line);
        }

        if ($this->hasCollector('messages')) {
            /** @var MessagesCollector $messagesCollector */
            $messagesCollector = $this['messages'];
            $file = $file ? ' on ' . $messagesCollector->normalizeFilePath($file) . ":{$line}" : '';
            $messagesCollector->addMessage($message . $file, 'deprecation');
        }

        if (! $this->prevErrorHandler) {
            return null;
        }

        return call_user_func($this->prevErrorHandler, $level, $message, $file, $line, $context);
    }

    /**
     * Starts a measure
     *
     * @param string      $name  Internal name, used to stop the measure
     * @param string|null $label Public name
     */
    public function startMeasure(string $name, ?string $label = null, ?string $collector = null, ?string $group = null): void
    {
        if ($this->hasCollector('time')) {
            /** @var \DebugBar\DataCollector\TimeDataCollector */
            $time = $this->getCollector('time');
            $time->startMeasure($name, $label, $collector, $group);
        }
    }

    /**
     * Stops a measure
     */
    public function stopMeasure(string $name): void
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
     * Alias for addThrowable
     *
     */
    public function addException(Throwable $e): void
    {
        $this->addThrowable($e);
    }

    /**
     * Adds an exception to be profiled in the debug bar
     */
    public function addThrowable(Throwable $e): void
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
     */
    protected function addCollectorException(string $message, Exception $exception)
    {
        $this->addThrowable(
            new Exception(
                $message . ' on Laravel Debugbar: ' . $exception->getMessage(),
                (int) $exception->getCode(),
                $exception,
            ),
        );
    }

    /**
     * Returns a JavascriptRenderer for this instance
     *
     */
    public function getJavascriptRenderer(?string $baseUrl = null, ?string $basePath = null): \DebugBar\JavascriptRenderer|JavascriptRenderer
    {
        if ($this->jsRenderer === null) {
            $this->jsRenderer = new JavascriptRenderer($this, $baseUrl, $basePath);
        }
        return $this->jsRenderer;
    }

    /**
     * Modify the response and inject the debugbar (or data in headers)
     */
    public function modifyResponse(Request $request, Response $response): Response
    {
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

        // These rely on the Response, so we add them directly here
        $sessionHiddens = $app['config']->get('debugbar.options.session.hiddens', []);
        $requestHiddens = array_merge(
            $app['config']->get('debugbar.options.symfony_request.hiddens', []),
            array_map(fn($key) => 'session_attributes.' . $key, $sessionHiddens),
        );
        $session = $request->hasSession() ? $request->getSession() : null;

        if ($session && $this->shouldCollect('session', false) && !$this->hasCollector('session')) {
            try {
                $this->addCollector(new SessionCollector($session, $sessionHiddens));
            } catch (Exception $e) {
                $this->addCollectorException('Cannot add SessionCollector', $e);
            }
        }

        if ($this->shouldCollect('symfony_request', true) && !$this->hasCollector('request')) {
            try {
                $reqId = $this->getCurrentRequestId();
                $this->addCollector(new RequestCollector($request, $response, $session, $reqId, $requestHiddens));
            } catch (Exception $e) {
                $this->addCollectorException('Cannot add SymfonyRequestCollector', $e);
            }
        }

        if ($app['config']->get('debugbar.clockwork') && ! $this->hasCollector('clockwork')) {
            try {
                $this->addCollector(new ClockworkCollector($request, $response, $session, $requestHiddens));
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
                    /** @var ViewCollector $viewCollector */
                    $viewCollector = $this['views'];
                    $viewCollector->addInertiaAjaxView($content);
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
            && !$this->isJsonResponse($response)
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
     */
    public function isEnabled(): bool
    {
        if ($this->enabled === null) {
            /** @var Repository $config */
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
     */
    protected function isDebugbarRequest(): bool
    {
        return $this->app['request']->is($this->app['config']->get('debugbar.route_prefix') . '*');
    }

    protected function isJsonRequest(Request $request, Response $response): bool
    {
        // If XmlHttpRequest, Live or HTMX, return true
        if (
            $request->isXmlHttpRequest()
            || $request->headers->has('X-Livewire')
            || ($request->headers->has('Hx-Request') && $request->headers->has('Hx-Target'))
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

    protected function isJsonResponse(Response $response): bool
    {
        if ($response->headers->get('Content-Type') == 'application/json') {
            return true;
        }

        $content = $response->getContent();
        if (is_string($content)) {
            $content = trim($content);
            if ($content === '') {
                return false;
            }

            // Quick check to see if it looks like JSON
            $first = $content[0];
            $last  = $content[strlen($content) - 1];
            if (
                ($first === '{' && $last === '}')
                || ($first === '[' && $last === ']')
            ) {
                // Must contain a colon or comma
                return strpbrk($content, ':,') !== false;
            }
        }

        return false;
    }

    /**
     * Collects the data from the collectors
     *
     */
    public function collect(): array
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
                'ip' => $request->getClientIp(),
            ],
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
            },
        );

        if ($this->storage !== null) {
            $this->storage->save($this->getCurrentRequestId(), $this->data);
        }

        return $this->data;
    }

    /**
     * Injects the web debug toolbar into the given Response.
     *
     * Based on https://github.com/symfony/WebProfilerBundle/blob/master/EventListener/WebDebugToolbarListener.php
     */
    public function injectDebugbar(Response $response): void
    {
        /** @var Repository $config */
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

        $widget = "<!-- Laravel Debugbar Widget -->\n" . $renderer->renderHead() . $renderer->render();

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
        if ($response instanceof \Illuminate\Http\Response && $original) {
            $response->original = $original;
        }
    }

    /**
     * Checks if there is stacked data in the session
     */
    public function hasStackedData(): bool
    {
        return count($this->getStackedData(false)) > 0;
    }

    /**
     * Returns the data stacked in the session
     *
     * @param bool $delete Whether to delete the data in the session
     */
    public function getStackedData(bool $delete = true): array
    {
        $this->stackedData = array_merge($this->stackedData, parent::getStackedData($delete));

        return $this->stackedData;
    }

    /**
     * Disable the Debugbar
     */
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * Adds a measure
     */
    public function addMeasure(string $label, float $start, float $end, array $params = [], ?string $collector = null, ?string $group = null): void
    {
        if ($this->hasCollector('time')) {
            /** @var \DebugBar\DataCollector\TimeDataCollector */
            $time = $this->getCollector('time');
            $time->addMeasure($label, $start, $end, $params, $collector, $group);
        }
    }

    /**
     * Utility function to measure the execution of a Closure
     */
    public function measure(string $label, \Closure $closure, ?string $collector = null, ?string $group = null): mixed
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
     */
    public function collectConsole(): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $this->data = [
            '__meta' => [
                'id' => $this->getCurrentRequestId(),
                'datetime' => date('Y-m-d H:i:s'),
                'utime' => microtime(true),
                'method' => 'CLI',
                'uri' => isset($_SERVER['argv']) ? implode(' ', $_SERVER['argv']) : null,
                'ip' => $_SERVER['SSH_CLIENT'] ?? null,
            ],
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
            },
        );

        if ($this->storage !== null) {
            $this->storage->save($this->getCurrentRequestId(), $this->data);
        }

        return $this->data;
    }

    /**
     * Magic calls for adding messages
     */
    public function __call(string $method, array $args): void
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
     */
    public function addMessage(mixed $message, string $label = 'info'): void
    {
        if ($this->hasCollector('messages')) {
            /** @var \DebugBar\DataCollector\MessagesCollector $collector */
            $collector = $this->getCollector('messages');
            $collector->addMessage($message, $label);
        }
    }

    /**
     * Check the version of Laravel
     */
    protected function checkVersion(string $version, string $operator = ">="): bool
    {
        return version_compare($this->version, $version, $operator);
    }

    protected function selectStorage(DebugBar $debugbar): void
    {
        /** @var Repository $config */
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
                    throw new \RuntimeException('Socket storage is not supported anymore.');
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

    protected function addClockworkHeaders(Response $response): void
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
     */
    protected function addServerTimingHeaders(Response $response): void
    {
        if ($this->hasCollector('time')) {
            $collector = $this->getCollector('time');

            $headers = [];
            foreach ($collector->collect()['measures'] as $m) {
                $headers[] = sprintf('app;desc="%s";dur=%F', str_replace(["\n", "\r"], ' ', str_replace('"', "'", $m['label'])), $m['duration'] * 1000);
            }

            $response->headers->set('Server-Timing', $headers, false);
        }
    }

    private function getRemoteServerReplacements(): array
    {
        $localPath = $this->app['config']->get('debugbar.local_sites_path') ?: base_path();
        $remotePaths = array_filter(explode(',', $this->app['config']->get('debugbar.remote_sites_path') ?: '')) ?: [base_path()];

        return array_fill_keys($remotePaths, $localPath);
    }

    private function getMonologLogger(): \Monolog\Logger
    {
        $logger = $this->app['log']->getLogger();

        if (get_class($logger) !== 'Monolog\Logger') {
            throw new Exception('Logger is not a Monolog\Logger instance');
        }

        return $logger;
    }
}
