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
use DebugBar\Storage\PdoStorage;
use DebugBar\Storage\RedisStorage;
use Exception;

use Laravel\Lumen\Application;


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
class LumenDebugbar extends LaravelDebugbar
{
    /**
     * The Laravel application instance.
     *
     * @var Application
     */
    protected $app;

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

        $this->selectStorage($debugbar);

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

        if ($this->shouldCollect('views', true) && isset($this->app['events'])) {
            try {
                $collectData = $this->app['config']->get('debugbar.options.views.data', true);
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

            if ($this->app['config']->get('debugbar.options.db.with_params')) {
                $queryCollector->setRenderSqlWithParams(true);
            }

            if ($this->app['config']->get('debugbar.options.db.backtrace')) {
                $queryCollector->setFindSource(true);
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


        if ($this->shouldCollect('logs', false)) {
            try {
                $file = $this->app['config']->get('debugbar.options.logs.file');
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
                    $this->app['config']->get('debugbar.options.auth.show_name')
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
        $renderer->setIncludeVendors($this->app['config']->get('debugbar.include_vendors', true));
        $renderer->setBindAjaxHandlerToXHR($app['config']->get('debugbar.capture_ajax', true));

        $this->booted = true;
    }


}
