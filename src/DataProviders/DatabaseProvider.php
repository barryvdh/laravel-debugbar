<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataProviders;

use Barryvdh\Debugbar\DataCollector\QueryCollector;
use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Events\ConnectionEstablished;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Routing\Router;

class DatabaseProvider extends AbstractDataProvider
{
    public function __invoke(Dispatcher $events, Router $router, array $config): void
    {
        if ($this->hasCollector('time') && ($config['timeline'] ?? false)) {
            /** @var TimeDataCollector $timeCollector */
            $timeCollector = $this['time'];
        } else {
            $timeCollector = null;
        }

        $queryCollector = new QueryCollector($timeCollector);
        $queryCollector->setLimits($config['soft_limit'] ?? 100, $config['hard_limit'] ?? 500);
        $queryCollector->setDurationBackground($config['duration_background'] ?? true);

        $threshold = $config['slow_threshold'] ?? false;
        if ($threshold && !($config['only_slow_queries'] ?? true)) {
            $queryCollector->setSlowThreshold($threshold);
        }

        if ($config['with_params'] ?? true) {
            $queryCollector->setRenderSqlWithParams(true);
        }

        if ($backtrace = ($config['backtrace'] ?? true)) {
            $queryCollector->setFindSource($backtrace, $router->getMiddleware());
        }

        if ($excludePaths = ($config['exclude_paths'] ?? [])) {
            $queryCollector->mergeExcludePaths($excludePaths);
        }

        if ($excludeBacktracePaths = ($config['backtrace_exclude_paths'] ?? [])) {
            $queryCollector->mergeBacktraceExcludePaths($excludeBacktracePaths);
        }

        if ($config['explain.enabled'] ?? false) {
            $queryCollector->setExplainSource(true);
        }

        $this->addCollector($queryCollector);

        try {
            $events->listen(
                function (QueryExecuted $query) use ($queryCollector, $config) {
                    // In case Debugbar is disabled after the listener was attached
                    if (!$this->debugbar->shouldCollect('db', true)) {
                        return;
                    }

                    $threshold = $config['slow_threshold'] ?? false;
                    $onlyThreshold = $config['only_slow_queries'] ?? true;

                    //allow collecting only queries slower than a specified amount of milliseconds
                    if (!$onlyThreshold || !$threshold || $query->time > $threshold) {
                        $queryCollector->addQuery($query);
                    }
                },
            );
        } catch (\Throwable $e) {
            $this->addCollectorException('Cannot listen to Queries', $e);
        }

        try {
            $events->listen(
                TransactionBeginning::class,
                fn($transaction) => $queryCollector->collectTransactionEvent('Begin Transaction', $transaction->connection),
            );

            $events->listen(
                TransactionCommitted::class,
                fn($transaction) => $queryCollector->collectTransactionEvent('Commit Transaction', $transaction->connection),
            );

            $events->listen(
                TransactionRolledBack::class,
                fn($transaction) => $queryCollector->collectTransactionEvent('Rollback Transaction', $transaction->connection),
            );

            $events->listen(
                'connection.*.beganTransaction',
                fn($event, $params) => $queryCollector->collectTransactionEvent('Begin Transaction', $params[0]),
            );

            $events->listen(
                'connection.*.committed',
                fn($event, $params) =>  $queryCollector->collectTransactionEvent('Commit Transaction', $params[0]),
            );

            $events->listen(
                'connection.*.rollingBack',
                fn($event, $params) => $queryCollector->collectTransactionEvent('Rollback Transaction', $params[0]),
            );

            $events->listen(
                function (ConnectionEstablished $event) use ($queryCollector, $config) {
                    $queryCollector->collectTransactionEvent('Connection Established', $event->connection);

                    if ($config['memory_usage'] ?? false) {
                        $event->connection->beforeExecuting(function () use ($queryCollector) {
                            $queryCollector->startMemoryUsage();
                        });
                    }
                },
            );
        } catch (\Throwable $e) {
            $this->addCollectorException('Cannot listen to Queries', $e);
        }
    }
}
