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

    public function testItInjectsOnPlainText()
    {
        $crawler = $this->call('GET', 'web/plain');

        static::assertTrue(Str::contains($crawler->content(), 'debugbar'));
        static::assertEquals(200, $crawler->getStatusCode());
        static::assertNotEmpty($crawler->headers->get('phpdebugbar-id'));
    }

    public function testItInjectsOnEmptyResponse()
    {
        $crawler = $this->call('GET', 'web/empty');

        static::assertTrue(Str::contains($crawler->content(), 'debugbar'));
        static::assertEquals(200, $crawler->getStatusCode());
        static::assertNotEmpty($crawler->headers->get('phpdebugbar-id'));
    }

    public function testItInjectsOnNullyResponse()
    {
        $crawler = $this->call('GET', 'web/null');

        static::assertTrue(Str::contains($crawler->content(), 'debugbar'));
        static::assertEquals(200, $crawler->getStatusCode());
        static::assertNotEmpty($crawler->headers->get('phpdebugbar-id'));
    }

    public function testItInjectsOnHtml()
    {
        $crawler = $this->call('GET', 'web/html');

        static::assertTrue(Str::contains($crawler->content(), 'debugbar'));
        static::assertEquals(200, $crawler->getStatusCode());
        static::assertNotEmpty($crawler->headers->get('phpdebugbar-id'));
    }

    public function testItDoesntInjectOnJson()
    {
        $crawler = $this->call('GET', 'api/ping');

        static::assertFalse(Str::contains($crawler->content(), 'debugbar'));
        static::assertEquals(200, $crawler->getStatusCode());
        static::assertNotEmpty($crawler->headers->get('phpdebugbar-id'));
    }

    public function testItDoesntInjectOnJsonLookingString()
    {
        $crawler = $this->call('GET', 'web/fakejson');

        static::assertFalse(Str::contains($crawler->content(), 'debugbar'));
        static::assertEquals(200, $crawler->getStatusCode());
        static::assertNotEmpty($crawler->headers->get('phpdebugbar-id'));
    }

    public function testItDoesntInjectsOnHxRequestWithHxTarget()
    {
        $crawler = $this->get('web/html', [
            'Hx-Request' => 'true',
            'Hx-Target' => 'main',
        ]);

        static::assertFalse(Str::contains($crawler->content(), 'debugbar'));
        static::assertEquals(200, $crawler->getStatusCode());
        static::assertNotEmpty($crawler->headers->get('phpdebugbar-id'));
    }

    public function testItInjectsOnHxRequestWithoutHxTarget()
    {
        $crawler = $this->get('web/html', [
            'Hx-Request' => 'true',
        ]);

        static::assertTrue(Str::contains($crawler->content(), 'debugbar'));
        static::assertEquals(200, $crawler->getStatusCode());
        static::assertNotEmpty($crawler->headers->get('phpdebugbar-id'));
    }
}
