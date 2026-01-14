<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Tests\DataCollector;

use Composer\InstalledVersions;
use Fruitcake\LaravelDebugbar\DataCollector\LivewireCollector;
use Fruitcake\LaravelDebugbar\ServiceProvider;
use Fruitcake\LaravelDebugbar\Tests\DataCollector\Livewire\DummyComponent;
use Fruitcake\LaravelDebugbar\Tests\TestCase;
use Livewire\Component;
use Livewire\LivewireServiceProvider;

class LivewireCollectorTest extends TestCase
{
    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class, LivewireServiceProvider::class];
    }

    public function testItCollectsLivewireComponents()
    {
        debugbar()->boot();

        /** @var \Fruitcake\LaravelDebugbar\DataCollector\GateCollector $collector */
        $collector = debugbar()->getCollector('livewire');

        $this->assertInstanceOf(LivewireCollector::class, $collector);

        if (version_compare(InstalledVersions::getVersion('livewire/livewire'), '3.0', '<')) {
            $component = new DummyComponent('123');
            $view = view('dashboard', ['_instance' => $component]);
            $collector->addLivewire2View($view, request());
        } else {
            $component = new DummyComponent();
            $component->setId('123');
            $component->setName('fruitcake.laravel-debugbar.tests.data-collector.livewire.dummy-component');
            $collector->addLivewireComponent($component, request());
        }

        $data = $collector->collect();

        $this->assertEquals('Fruitcake\LaravelDebugbar\Tests\DataCollector\Livewire\DummyComponent fruitcake.laravel-debugbar.tests.data-collector.livewire.dummy-component #123', $data['templates'][0]['name']);
        $this->assertStringContainsString('MyComponent', $data['templates'][0]['params']['title']);
    }

    public function testItCollectsAnonymousLivewireComponents()
    {
        debugbar()->boot();

        /** @var \Fruitcake\LaravelDebugbar\DataCollector\GateCollector $collector */
        $collector = debugbar()->getCollector('livewire');

        $this->assertInstanceOf(LivewireCollector::class, $collector);

        $component = new class extends Component {
            public $title = 'MyComponent';
        };

        if (version_compare(InstalledVersions::getVersion('livewire/livewire'), '3.0', '<')) {
            $component->id = '123';
            $view = view('dashboard', ['_instance' => $component]);
            $collector->addLivewire2View($view, request());
        } else {
            $component->setId('123');
            $component->setName('fruitcake.laravel-debugbar.tests.data-collector.livewire.dummy-component');
            $collector->addLivewireComponent($component, request());
        }

        $data = $collector->collect();

        if (version_compare(InstalledVersions::getVersion('livewire/livewire'), '3.0', '<')) {
            $this->assertStringContainsString('livewire.component@anonymous.users.barry.sites.laravel-debugbar.tests.data-collector.livewire-collector-test.php:', $data['templates'][0]['name']);
        } else {
            $this->assertEquals('fruitcake.laravel-debugbar.tests.data-collector.livewire.dummy-component #123', $data['templates'][0]['name']);
        }
        $this->assertStringContainsString('MyComponent', $data['templates'][0]['params']['title']);
    }
}
