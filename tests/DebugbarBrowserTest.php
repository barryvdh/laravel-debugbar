<?php

namespace Barryvdh\Debugbar\Tests;

use Illuminate\Routing\Router;
use Laravel\Dusk\Browser;

class DebugbarBrowserTest extends BrowserTestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['env'] = 'local';

//        $app['config']->set('app.debug', true);

        /** @var Router $router */
        $router = $app['router'];

        $this->addWebRoutes($router);
        $this->addApiRoutes($router);

        $kernel = app('Illuminate\Contracts\Http\Kernel');
        $kernel->pushMiddleware('Illuminate\Session\Middleware\StartSession');

        \Orchestra\Testbench\Dusk\Options::withoutUI();
    }

    /**
     * @param Router $router
     */
    protected function addWebRoutes(Router $router)
    {
        $router->get('web/redirect', [
            'uses' => function () {
                return redirect($this->applicationBaseUrl() . '/web/plain');
            }
        ]);

        $router->get('web/plain', [
            'uses' => function () {
                return 'PONG';
            }
        ]);

        $router->get('web/html', [
            'uses' => function () {
                return '<html><head></head><body>HTMLPONG</body></html>';
            }
        ]);
    }

    /**
     * @param Router $router
     */
    protected function addApiRoutes(Router $router)
    {
        $router->get('api/ping', [
            'uses' => function () {
                return response()->json(['status' => 'pong']);
            }
        ]);
    }

    public function testItStacksOnRedirect()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('web/redirect')
                ->assertSee('PONG')
                ->waitFor('.phpdebugbar-fa-history')
                ->assertSee('GET web/plain')
                ->click('.phpdebugbar-tab-history')
                ->waitFor('.phpdebugbar-widgets-dataset-history')
                ->waitForTextIn('.phpdebugbar-widgets-dataset-history', 'web/redirect (stacked)')
                ->assertSee('web/redirect');
        });
    }

    public function testItInjectsOnPlainText()
    {
        $this->browse(function ($browser) {
            $browser->visit('web/plain')
                ->assertSee('PONG')
                ->waitFor('.phpdebugbar')
                ->assertSee('GET web/plain');
        });
    }

    public function testItInjectsOnHtml()
    {
        $this->browse(function ($browser) {
            $browser->visit('web/html')
                ->assertSee('HTMLPONG')
                ->waitFor('.phpdebugbar')
                ->assertSee('GET web/html');
        });
    }

    public function testItDoesntInjectOnJson()
    {
        $this->browse(function ($browser) {
            $browser->visit('api/ping')
                ->assertSee('pong')
                ->assertSourceMissing('debugbar')
                ->assertDontSee('GET api/ping');
        });
    }
}
