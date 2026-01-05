<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\Tests;

use Barryvdh\Debugbar\LaravelDebugbar;
use Barryvdh\Debugbar\Tests\Models\User;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;

class DebugbarDocsTest extends TestCase
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

        // Force the Debugbar to Enable on test/cli applications
        $app->resolving(LaravelDebugbar::class, function ($debugbar) {
            $refObject = new \ReflectionObject($debugbar);
            $refProperty = $refObject->getProperty('enabled');
            $refProperty->setValue($debugbar, true);
        });
    }

    public function testItInjectsOnDocs()
    {
        /** @var Router $router */
        $router = $this->app['router'];
        $this->app['config']->set('debugbar.hide_empty_tabs', true);

        $this->loadLaravelMigrations();

        $router->get('docs', function () {
            debugbar()->addMessage('Hello Artisans!');
            debugbar()->warning('Watch out for ..');
            debugbar()->error('Bugs!');

            User::create(['email' => 'demo@example.com', 'name' => 'Barry', 'password' => bcrypt('secret')]);
            User::count();
            User::where('name', 'Barry')->first();
            User::where('id', 1)->get();
            User::where('id', 1)->get();
            User::where('id', 1)->get();

            view('dashboard')->render();

            debugbar()->addException(new \RuntimeException('Whoops! This is just a demo'));

            return '';
        });

        $crawler = $this->call('GET', 'docs');

        $this->assertTrue(Str::contains($crawler->content(), 'debugbar'));
        $this->assertNotEmpty($crawler->headers->get('phpdebugbar-id'));

        @mkdir(__DIR__ . '/../build/docs/assets', 0o777, true);

        // Store output for test
        file_put_contents(__DIR__ . '/../build/docs/render.html', $crawler->getContent());

        file_put_contents(__DIR__ . '/../build/docs/assets/debugbar.css', $this->call('GET', '/_debugbar/assets/stylesheets')->getContent());
        file_put_contents(__DIR__ . '/../build/docs/assets/debugbar.js', $this->call('GET', '/_debugbar/assets/javascript')->getContent());

    }
}
