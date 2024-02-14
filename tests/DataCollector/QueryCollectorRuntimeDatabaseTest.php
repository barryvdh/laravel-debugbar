<?php

namespace Barryvdh\Debugbar\Tests\DataCollector;

use Barryvdh\Debugbar\Tests\TestCase;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;

class QueryCollectorRuntimeDatabaseTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', null);

        $app['config']->set('database.connections', []);
    }

    public function testItReplacesQuestionMarksBindingsCorrectly()
    {
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

            self::assertSame('SELECT 1', $collection['statements'][2]['sql']);
        });
    }
}
