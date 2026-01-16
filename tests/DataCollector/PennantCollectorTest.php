<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Tests\DataCollector;

use Composer\InstalledVersions;
use Fruitcake\LaravelDebugbar\DataCollector\LivewireCollector;
use Fruitcake\LaravelDebugbar\DataCollector\PennantCollector;
use Fruitcake\LaravelDebugbar\ServiceProvider;
use Fruitcake\LaravelDebugbar\Tests\DataCollector\Livewire\DummyComponent;
use Fruitcake\LaravelDebugbar\Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Laravel\Pennant\PennantServiceProvider;

use function Orchestra\Testbench\artisan;


class PennantCollectorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class, PennantServiceProvider::class];
    }

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        $reflection = new \ReflectionClass(Feature::class);
        // Load Pennant migrations
        $this->loadMigrationsFrom(
            dirname($reflection->getFileName()) . '/../database/migrations'
        );
    }

    public function testItCollectsPennantValues()
    {
        debugbar()->boot();

        Feature::define('new-api', true);
        Feature::define('old-api', fn () => false);
        Feature::define('api-version', fn () => '3.x');

        /** @var \Fruitcake\LaravelDebugbar\DataCollector\GateCollector $collector */
        $collector = debugbar()->getCollector('pennant');

        $this->assertInstanceOf(PennantCollector::class, $collector);
        $data = $collector->collect();

        dd($data);
        $this->assertCount(3, $data['pennant']);
        $this->assertTrue($data['pennant']['new-api']);
        $this->assertFalse($data['pennant']['old-api']);
        $this->assertEquals('3.x', $data['pennant']['old-api']);
    }
}
