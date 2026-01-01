<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\Tests\DataCollector;

use Barryvdh\Debugbar\Tests\TestCase;
use Illuminate\Database\Connection;

class QueryCollectorRuntimeDatabaseTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', null);

        $app['config']->set('database.connections', []);
    }

    public function testCollectsQueriesFromRuntimeConnections()
    {
        if (version_compare($this->app->version(), '10', '<')) {
            $this->markTestSkipped('This test is not compatible with Laravel 9.x and below');
        }

        debugbar()->boot();

        /** @var Connection $connection */
        $connection = $this->app['db']->connectUsing(
            'runtime-connection',
            [
                'driver' => 'sqlite',
                'database' => ':memory:',
            ],
        );

        $connection->statement('SELECT 1');

        /** @var \Debugbar\DataCollector\ExceptionsCollector $collector */
        $exceptions = debugbar()->getCollector('exceptions');

        self::assertEmpty($exceptions->getExceptions());

        /** @var \Barryvdh\Debugbar\DataCollector\QueryCollector $collector */
        $collector  = debugbar()->getCollector('queries');

        tap($collector->collect(), function (array $collection) {
            $this->assertEquals(1, $collection['nb_statements']);

            self::assertSame('SELECT 1', $collection['statements'][1]['sql']);
        });
    }

    public function testCollectsQueriesFromRuntimeConnectionsWithoutConnectUsing()
    {
        debugbar()->boot();

        $this->app['config']->set('database.connections.dynamic-connection', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        $this->app['config']->set('database.default', 'dynamic-connection');

        /** @var Connection $connection */
        $connection = $this->app['db']->connection('dynamic-connection');

        $connection->statement('SELECT 1');

        /** @var \Debugbar\DataCollector\ExceptionsCollector $collector */
        $exceptions = debugbar()->getCollector('exceptions');

        self::assertEmpty($exceptions->getExceptions());

        /** @var \Barryvdh\Debugbar\DataCollector\QueryCollector $collector */
        $collector  = debugbar()->getCollector('queries');

        tap($collector->collect(), function (array $collection) {
            $this->assertEquals(1, $collection['nb_statements']);

            self::assertSame('SELECT 1', $collection['statements'][1]['sql']);
        });
    }
}
