<?php

namespace Barryvdh\Debugbar\Tests\DataCollector;

use Barryvdh\Debugbar\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;

class QueryCollectorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_replaces_question_marks_bindings_correctly()
    {
        $this->loadLaravelMigrations();

        $this->debugbar()->boot();

        /** @var \Barryvdh\Debugbar\DataCollector\QueryCollector $collector */
        $collector  = $this->debugbar()->getCollector('queries');
        $collector->addQuery(
            "SELECT ('[1, 2, 3]'::jsonb ?? ?) as a, ('[4, 5, 6]'::jsonb ??| ?) as b, 'hello world ? example ??' as c",
            [3, '{4}'],
            0,
            $this->app['db']->connection()
        );

        tap($collector->collect(), function (array $collection) {
            $this->assertEquals(1, $collection['nb_statements']);

            tap(Arr::first($collection['statements']), function (array $statement) {
                $this->assertEquals([3, '{4}'], $statement['bindings']);
                $this->assertEquals("SELECT ('[1, 2, 3]'::jsonb ? 3) as a, ('[4, 5, 6]'::jsonb ?| '{4}') as b, 'hello world ? example ??' as c", $statement['sql']);
            });
        });
    }
}
