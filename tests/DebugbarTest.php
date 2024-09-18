<?php

namespace Barryvdh\Debugbar\Tests;

use Barryvdh\Debugbar\LaravelDebugbar;
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
        parent::getEnvironmentSetUp($app);

        // Force the Debugbar to Enable on test/cli applications
        $app->resolving(LaravelDebugbar::class, function ($debugbar) {
                $refObject = new \ReflectionObject($debugbar);
                $refProperty = $refObject->getProperty('enabled');
                $refProperty->setAccessible(true);
                $refProperty->setValue($debugbar, true);
        });
    }

    public function testItInjectsOnPlainText()
    {
        $crawler = $this->call('GET', 'web/plain');

        $this->assertTrue(Str::contains($crawler->content(), 'debugbar'));
        $this->assertEquals(200, $crawler->getStatusCode());
        $this->assertNotEmpty($crawler->headers->get('phpdebugbar-id'));
    }

    public function testItInjectsOnHtml()
    {
        $crawler = $this->call('GET', 'web/html');

        $this->assertTrue(Str::contains($crawler->content(), 'debugbar'));
        $this->assertEquals(200, $crawler->getStatusCode());
        $this->assertNotEmpty($crawler->headers->get('phpdebugbar-id'));
    }

    public function testItDoesntInjectOnJson()
    {
        $crawler = $this->call('GET', 'api/ping');

        $this->assertFalse(Str::contains($crawler->content(), 'debugbar'));
        $this->assertEquals(200, $crawler->getStatusCode());
        $this->assertNotEmpty($crawler->headers->get('phpdebugbar-id'));
    }

    public function testItDoesntInjectOnJsonLookingString()
    {
        $crawler = $this->call('GET', 'web/fakejson');

        $this->assertFalse(Str::contains($crawler->content(), 'debugbar'));
        $this->assertEquals(200, $crawler->getStatusCode());
        $this->assertNotEmpty($crawler->headers->get('phpdebugbar-id'));
    }

    public function testItDoesntInjectsOnHxRequestWithHxTarget()
    {
        $crawler = $this->get('web/html', [
            'Hx-Request' => 'true',
            'Hx-Target' => 'main',
        ]);

        $this->assertFalse(Str::contains($crawler->content(), 'debugbar'));
        $this->assertEquals(200, $crawler->getStatusCode());
        $this->assertNotEmpty($crawler->headers->get('phpdebugbar-id'));
    }

    public function testItInjectsOnHxRequestWithoutHxTarget()
    {
        $crawler = $this->get('web/html', [
            'Hx-Request' => 'true',
        ]);

        $this->assertTrue(Str::contains($crawler->content(), 'debugbar'));
        $this->assertEquals(200, $crawler->getStatusCode());
        $this->assertNotEmpty($crawler->headers->get('phpdebugbar-id'));
    }
}
