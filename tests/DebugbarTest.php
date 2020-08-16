<?php

namespace Barryvdh\Debugbar\Tests;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;

class DebugbarTest extends TestCase
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
        // Simulate local setup
        $_ENV['APP_RUNNING_IN_CONSOLE'] = 0;
        $app['env'] = 'local';

        parent::getEnvironmentSetUp($app);
    }

    public function testItInjectsOnPlainText()
    {
        $crawler = $this->call('GET', 'web/plain');

        $this->assertTrue(Str::contains($crawler->content(), 'debugbar'));
        $this->assertEquals(200, $crawler->getStatusCode());
    }

    public function testItInjectsOnHtml()
    {
        $crawler = $this->call('GET', 'web/html');

        $this->assertTrue(Str::contains($crawler->content(), 'debugbar'));
        $this->assertEquals(200, $crawler->getStatusCode());
    }

    public function testItDoesntInjectOnJson()
    {
        $crawler = $this->call('GET', 'api/ping');

        $this->assertFalse(Str::contains($crawler->content(), 'debugbar'));
        $this->assertEquals(200, $crawler->getStatusCode());
    }

}