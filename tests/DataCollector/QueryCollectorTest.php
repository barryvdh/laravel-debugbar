<?php

namespace Barryvdh\Debugbar\Tests\DataCollector;

use Barryvdh\Debugbar\Tests\TestCase;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;

class QueryCollectorTest extends TestCase
{
    use RefreshDatabase;

    public function testItReplacesQuestionMarksBindingsCorrectly()
    {
        $this->loadLaravelMigrations();

        debugbar()->boot();

        /** @var \Barryvdh\Debugbar\DataCollector\QueryCollector $collector */
        $collector  = debugbar()->getCollector('queries');
        $collector->addQuery(new QueryExecuted(
            "SELECT ('[1, 2, 3]'::jsonb ?? ?) as a, ('[4, 5, 6]'::jsonb ??| ?) as b, 'hello world ? example ??' as c",
            [3, '{4}'],
            0,
            $this->app['db']->connection(),
        ));

        tap($collector->collect(), function (array $collection) {
            $this->assertEquals(1, $collection['nb_statements']);

            tap(Arr::first($collection['statements']), function (array $statement) {
                $this->assertEquals([3, '{4}'], $statement['bindings']);
                $this->assertEquals(<<<SQL
SELECT ('[1, 2, 3]'::jsonb ? 3) as a, ('[4, 5, 6]'::jsonb ?| '{4}') as b, 'hello world ? example ??' as c
SQL
                    , $statement['sql']);
            });
        });
    }

    public function testDollarBindingsArePresentedCorrectly()
    {
        debugbar()->boot();

        /** @var \Barryvdh\Debugbar\DataCollector\QueryCollector $collector */
        $collector = debugbar()->getCollector('queries');
        $collector->addQuery(new QueryExecuted(
            "SELECT a FROM b WHERE c = ? AND d = ? AND e = ?",
            ['$10', '$2y$10_DUMMY_BCRYPT_HASH', '$_$$_$$$_$2_$3'],
            0,
            $this->app['db']->connection(),
        ));

        tap(Arr::first($collector->collect()['statements']), function (array $statement) {
            $this->assertEquals(
                "SELECT a FROM b WHERE c = '$10' AND d = '$2y$10_DUMMY_BCRYPT_HASH' AND e = '\$_$\$_$$\$_$2_$3'",
                $statement['sql'],
            );
        });
    }

    public function testFindingCorrectPathForView()
    {
        debugbar()->boot();

        /** @var \Barryvdh\Debugbar\DataCollector\QueryCollector $collector */
        $collector = debugbar()->getCollector('queries');

        view('query')
            ->with('db', $this->app['db']->connection())
            ->with('collector', $collector)
            ->render();

        tap(Arr::first($collector->collect()['statements']), function (array $statement) {
            $this->assertEquals(
                "SELECT a FROM b WHERE c = '$10' AND d = '$2y$10_DUMMY_BCRYPT_HASH' AND e = '\$_$\$_$$\$_$2_$3'",
                $statement['sql'],
            );

            $this->assertTrue(@file_exists($statement['backtrace'][1]->file));
            $this->assertEquals(
                realpath(__DIR__ . '/../resources/views/query.blade.php'),
                realpath($statement['backtrace'][1]->file),
            );
        });
    }
}
