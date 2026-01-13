<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Tests;

use Fruitcake\LaravelDebugbar\LaravelDebugbar;
use Illuminate\Support\Str;

class DebugbarTest extends TestCase
{
    /**
     * Define environment setup.
     *
     *
     */
    protected function getEnvironmentSetUp(\Illuminate\Foundation\Application $app): void
    {
        parent::getEnvironmentSetUp($app);

        // Force the Debugbar to Enable on test/cli applications
        $app->resolving(LaravelDebugbar::class, function ($debugbar) {
            $refObject = new \ReflectionObject($debugbar);
            $refProperty = $refObject->getProperty('enabled');
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

    public function testItInjectsOnEmptyResponse()
    {
        $crawler = $this->call('GET', 'web/empty');

        $this->assertTrue(Str::contains($crawler->content(), 'debugbar'));
        $this->assertEquals(200, $crawler->getStatusCode());
        $this->assertNotEmpty($crawler->headers->get('phpdebugbar-id'));
    }

    public function testItInjectsOnNullyResponse()
    {
        $crawler = $this->call('GET', 'web/null');

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
