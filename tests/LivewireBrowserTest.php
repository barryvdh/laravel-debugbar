<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Tests;

use Fruitcake\LaravelDebugbar\ServiceProvider;
use Fruitcake\LaravelDebugbar\Tests\DataCollector\Livewire\DummyComponent;
use Illuminate\Routing\Router;
use Laravel\Dusk\Browser;
use Livewire\LivewireServiceProvider;

class LivewireBrowserTest extends BrowserTestCase
{
    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['env'] = 'local';

        //$app['config']->set('app.debug', true);
        $app['config']->set('debugbar.hide_empty_tabs', false);
        config(['view.paths' => array_merge(config('view.paths'), [__DIR__ . '/resources/views'])]);

        /** @var Router $router */
        $router = $app['router'];

        $router->get('web/livewire', [
            'uses' => DummyComponent::class,
        ]);

        $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
        $kernel->pushMiddleware(\Illuminate\Session\Middleware\StartSession::class);
        $kernel->pushMiddleware(\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class);

        \Orchestra\Testbench\Dusk\Options::withoutUI();
    }

    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class, LivewireServiceProvider::class];
    }
    public function testLivewireCollectsComponents()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('web/livewire')
                ->waitFor('.phpdebugbar')
                ->click('.phpdebugbar-tab-settings')
                ->waitForTextIn('.phpdebugbar-tab[data-collector="livewire"] .phpdebugbar-badge', 1)
                ->click('.phpdebugbar-tab[data-collector="livewire"]')
                ->assertSee('1 Livewire component')
//                ->assertSee('You are #1') // TODO; fix renders 
                ->with('.phpdebugbar-widgets-list-item', function ($queriesPane) {
                    $queriesPane->assertSee('DummyComponent')
                        ->click('.phpdebugbar-widgets-name')
                        ->assertSee('Params')
                        ->assertSee('title')
                        ->assertSee('MyComponent');
                })
                ->click('.phpdebugbar-tab[data-collector="request"]')
                ->assertSee('Tests\DataCollector\Livewire\DummyComponent');
        });
    }
}
