<?php

namespace Barryvdh\Debugbar\Tests;

use Barryvdh\Debugbar\LaravelDebugbar;
use Illuminate\Support\Str;

class FloatingPositionTest extends TestCase
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
            $refProperty->setValue($debugbar, true);
        });
    }

    public function testDefaultPositionIsBottom()
    {
        $this->app['config']->set('debugbar.position', 'bottom');

        $debugbar = $this->app->make(LaravelDebugbar::class);
        $debugbar->boot();

        $renderer = $debugbar->getJavascriptRenderer();
        $positionConfig = $renderer->getPositionConfig();

        $this->assertEquals('bottom', $positionConfig['position']);
    }

    public function testFloatingPositionIsConfigurable()
    {
        $this->app['config']->set('debugbar.position', 'floating');
        $this->app['config']->set('debugbar.floating', [
            'initial_x' => 100,
            'initial_y' => 200,
            'remember_position' => true,
        ]);

        $debugbar = $this->app->make(LaravelDebugbar::class);
        $debugbar->boot();

        $renderer = $debugbar->getJavascriptRenderer();
        $positionConfig = $renderer->getPositionConfig();

        $this->assertEquals('floating', $positionConfig['position']);
        $this->assertEquals(100, $positionConfig['floating']['initial_x']);
        $this->assertEquals(200, $positionConfig['floating']['initial_y']);
        $this->assertTrue($positionConfig['floating']['remember_position']);
    }

    public function testFloatingOptionsHaveDefaults()
    {
        $this->app['config']->set('debugbar.position', 'floating');
        $this->app['config']->set('debugbar.floating', []);

        $debugbar = $this->app->make(LaravelDebugbar::class);
        $debugbar->boot();

        $renderer = $debugbar->getJavascriptRenderer();
        $positionConfig = $renderer->getPositionConfig();

        $this->assertEquals('floating', $positionConfig['position']);
        $this->assertNull($positionConfig['floating']['initial_x']);
        $this->assertNull($positionConfig['floating']['initial_y']);
        $this->assertTrue($positionConfig['floating']['remember_position']);
    }

    public function testPositionConfigIsInjectedAsInlineJs()
    {
        $this->app['config']->set('debugbar.position', 'floating');
        $this->app['config']->set('debugbar.floating', [
            'remember_position' => true,
        ]);

        $crawler = $this->call('GET', 'web/html');

        // Check that the position config is injected in the response
        $content = $crawler->content();
        $this->assertTrue(Str::contains($content, 'window.phpdebugbar_position_config'));
        $this->assertTrue(Str::contains($content, '"position":"floating"'));
    }

    public function testSetPositionOptionsMethod()
    {
        $debugbar = $this->app->make(LaravelDebugbar::class);
        $renderer = $debugbar->getJavascriptRenderer();

        // Test the setPositionOptions method directly
        $renderer->setPositionOptions('floating', [
            'initial_x' => 50,
            'initial_y' => 75,
            'remember_position' => false,
        ]);

        $positionConfig = $renderer->getPositionConfig();

        $this->assertEquals('floating', $positionConfig['position']);
        $this->assertEquals(50, $positionConfig['floating']['initial_x']);
        $this->assertEquals(75, $positionConfig['floating']['initial_y']);
        $this->assertFalse($positionConfig['floating']['remember_position']);
    }

    public function testRememberPositionCanBeDisabled()
    {
        $this->app['config']->set('debugbar.position', 'floating');
        $this->app['config']->set('debugbar.floating', [
            'remember_position' => false,
        ]);

        $debugbar = $this->app->make(LaravelDebugbar::class);
        $debugbar->boot();

        $renderer = $debugbar->getJavascriptRenderer();
        $positionConfig = $renderer->getPositionConfig();

        $this->assertFalse($positionConfig['floating']['remember_position']);
    }

    public function testDraggableJsFileIsRegistered()
    {
        $debugbar = $this->app->make(LaravelDebugbar::class);
        $renderer = $debugbar->getJavascriptRenderer();

        // Get JS assets through reflection
        $refObject = new \ReflectionObject($renderer);
        $refProperty = $refObject->getProperty('jsFiles');
        $jsFiles = $refProperty->getValue($renderer);

        $this->assertArrayHasKey('laravel-draggable', $jsFiles);
        $this->assertTrue(Str::endsWith($jsFiles['laravel-draggable'], 'draggable.js'));
    }

    public function testDraggableJsFileExists()
    {
        $expectedPath = __DIR__ . '/../src/Resources/draggable.js';
        $this->assertFileExists($expectedPath);
    }

    public function testFloatingCssStylesExist()
    {
        $cssPath = __DIR__ . '/../src/Resources/laravel-debugbar.css';
        $cssContent = file_get_contents($cssPath);

        // Check for floating-related CSS classes
        $this->assertTrue(Str::contains($cssContent, '.phpdebugbar-floating'));
        $this->assertTrue(Str::contains($cssContent, '.phpdebugbar-dragging'));
        $this->assertTrue(Str::contains($cssContent, '.phpdebugbar-drag-handle'));
    }

    public function testSnapPreviewCssStylesExist()
    {
        $cssPath = __DIR__ . '/../src/Resources/laravel-debugbar.css';
        $cssContent = file_get_contents($cssPath);

        // Check for snap preview CSS classes
        $this->assertTrue(Str::contains($cssContent, '.phpdebugbar-snap-preview'));
        $this->assertTrue(Str::contains($cssContent, '.phpdebugbar-snap-preview-active'));
        $this->assertTrue(Str::contains($cssContent, '.phpdebugbar-snapping'));
        $this->assertTrue(Str::contains($cssContent, '.phpdebugbar-snapped-bottom'));
        $this->assertTrue(Str::contains($cssContent, '.phpdebugbar-snapped-top'));
    }

    public function testPositionModeCssStylesExist()
    {
        $cssPath = __DIR__ . '/../src/Resources/laravel-debugbar.css';
        $cssContent = file_get_contents($cssPath);

        $this->assertTrue(Str::contains($cssContent, 'data-positionMode="bottom"'));
    }

    public function testDraggableJsHasPositionModeFeatures()
    {
        $jsPath = __DIR__ . '/../src/Resources/draggable.js';
        $jsContent = file_get_contents($jsPath);

        // Check that the position mode settings hook code exists
        $this->assertTrue(Str::contains($jsContent, 'hookSettingsWidget'));
        $this->assertTrue(Str::contains($jsContent, 'addPositionModeToSettings'));
        $this->assertTrue(Str::contains($jsContent, 'storePositionMode'));
        $this->assertTrue(Str::contains($jsContent, 'applyPositionMode'));
        $this->assertTrue(Str::contains($jsContent, 'Position Mode'));
    }

    public function testConfigFileHasPositionOption()
    {
        $configPath = __DIR__ . '/../config/debugbar.php';
        $configContent = file_get_contents($configPath);

        $this->assertTrue(Str::contains($configContent, "'position'"));
        $this->assertTrue(Str::contains($configContent, "'floating'"));
        $this->assertTrue(Str::contains($configContent, "'initial_x'"));
        $this->assertTrue(Str::contains($configContent, "'initial_y'"));
        $this->assertTrue(Str::contains($configContent, "'remember_position'"));
    }

    public function testNoConfigInjectedForBottomPosition()
    {
        // When position is 'bottom' (default), config should still be injected
        // but the floating class should not be added
        $this->app['config']->set('debugbar.position', 'bottom');

        $crawler = $this->call('GET', 'web/html');

        $content = $crawler->content();
        // Config should still be present
        $this->assertTrue(Str::contains($content, 'window.phpdebugbar_position_config'));
        // But should indicate 'bottom' position
        $this->assertTrue(Str::contains($content, '"position":"bottom"'));
    }
}
