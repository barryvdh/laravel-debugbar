<?php

namespace Barryvdh\Debugbar\Tests\DataCollector;

use Barryvdh\Debugbar\Tests\Jobs\OrderShipped;
use Barryvdh\Debugbar\Tests\Jobs\SendNotification;
use Barryvdh\Debugbar\Tests\TestCase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

class JobsCollectorTest extends TestCase
{
    use RefreshDatabase;

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('debugbar.collectors.jobs', true);
        // The `sync` and `null` driver don't dispatch events
        // `database` or `redis` driver work great
        $app['config']->set('queue.default', 'database');

        parent::getEnvironmentSetUp($app);
    }

    public function testItCollectsDispatchedJobs()
    {
        $this->loadLaravelMigrations();
        $this->createJobsTable();

        debugbar()->boot();

        /** @var \Barryvdh\Debugbar\DataCollector\ModelsCollector $collector */
        $collector = debugbar()->getCollector('jobs');

        $this->assertEquals(
            ['data' => [], 'count' => 0],
            $collector->collect()
        );

        OrderShipped::dispatch(1);

        $this->assertEquals(
            ['data' => [OrderShipped::class => 1], 'count' => 1],
            $collector->collect()
        );

        dispatch(new SendNotification());
        dispatch(new SendNotification());
        dispatch(new SendNotification());

        $this->assertEquals(
            ['data' => [OrderShipped::class => 1, SendNotification::class => 3], 'count' => 4],
            $collector->collect()
        );
    }

    protected function createJobsTable()
    {
        (new class extends Migration
        {
            public function up()
            {
                Schema::create('jobs', function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->string('queue')->index();
                    $table->longText('payload');
                    $table->unsignedTinyInteger('attempts');
                    $table->unsignedInteger('reserved_at')->nullable();
                    $table->unsignedInteger('available_at');
                    $table->unsignedInteger('created_at');
                });
            }
        })->up();
    }
}
