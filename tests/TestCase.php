<?php

namespace Barryvdh\Debugbar\Tests;

use Barryvdh\Debugbar\Facades\Debugbar;
use Barryvdh\Debugbar\ServiceProvider;
use Illuminate\Routing\Router;
use Orchestra\Testbench\TestCase as Orchestra;
use Barryvdh\Debugbar\Tests\Mocks\MockController;
use Barryvdh\Debugbar\Tests\Mocks\MockViewComponent;
use Barryvdh\Debugbar\Tests\Mocks\MockMiddleware;

class TestCase extends Orchestra
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return ['Debugbar' => Debugbar::class];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        /** @var Router $router */
        $router = $app['router'];

        $this->addWebRoutes($router);
        $this->addApiRoutes($router);
        $this->addViewPaths();
    }

    /**
     * @param Router $router
     */
    protected function addWebRoutes(Router $router)
    {
        $router->get('web/plain', function () {
            return 'PONG';
        });

        $router->get('web/html', function () {
            return '<html><head></head><body>Pong</body></html>';
        });

        $router->get('web/show', [ MockController::class, 'show' ]);

        $router->get('web/view', MockViewComponent::class);

        $router->post('web/mw')->middleware(MockMiddleware::class);
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

    protected function addViewPaths()
    {
        config(['view.paths' => array_merge(config('view.paths'), [__DIR__ . '/resources/views'])]);
    }
}
