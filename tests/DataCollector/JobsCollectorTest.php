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

        /** @var \DebugBar\DataCollector\ObjectCountCollector $collector */
        $collector = debugbar()->getCollector('jobs');
        $collector->setXdebugLinkTemplate('');
        $collector->setKeyMap([]);
        $data = [];

        $this->assertEquals(
            ['data' => $data, 'count' => 0, 'key_map' => [], 'is_counter' => true],
            $collector->collect()
        );

        OrderShipped::dispatch(1);

        $data[OrderShipped::class] = ['value' => 1];
        $this->assertEquals(
            ['data' => $data, 'count' => 1, 'key_map' => [], 'is_counter' => true],
            $collector->collect()
        );

        dispatch(new SendNotification());
        dispatch(new SendNotification());
        dispatch(new SendNotification());

        $data[SendNotification::class] = ['value' => 3];
        $this->assertEquals(
            ['data' => $data, 'count' => 4, 'key_map' => [], 'is_counter' => true],
            $collector->collect()
        );
    }

    protected function createJobsTable()
    {
        (new class extends Migration
        {
            public function up()
            {
                if (Schema::hasTable('jobs')) {
                    return;
                }

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
