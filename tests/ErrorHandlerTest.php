<?php

namespace Barryvdh\Debugbar\Tests;

use Barryvdh\Debugbar\LaravelDebugbar;
use ReflectionObject;

class ErrorHandlerTest extends TestCase
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
            $refObject = new ReflectionObject($debugbar);
            $refProperty = $refObject->getProperty('enabled');
            $refProperty->setValue($debugbar, true);
        });

        // Enable collectors needed for error handling
        $app['config']->set('debugbar.collectors.messages', true);
        $app['config']->set('debugbar.collectors.exceptions', true);
    }

    public function testErrorHandlerRespectsCustomErrorLevel()
    {
        $app = $this->app;
        $app['config']->set('debugbar.error_handler', true);
        // Exclude deprecation warnings
        $app['config']->set('debugbar.error_level', E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        $debugbar = $app->make(LaravelDebugbar::class);
        $debugbar->boot();

        // Get initial message count
        $initialCount = 0;
        if ($debugbar->hasCollector('messages')) {
            $initialCount = count($debugbar->getCollector('messages')->collect()['messages']);
        }

        // Trigger a deprecation warning - should NOT be captured
        @trigger_error('Test deprecation warning', E_USER_DEPRECATED);

        // Check that error was NOT captured
        if ($debugbar->hasCollector('messages')) {
            $messages = $debugbar->getCollector('messages')->collect();
            $newCount = count($messages['messages']);
            $this->assertEquals($initialCount, $newCount, 'Deprecation warning should not be captured when excluded from error_level');
        }

        // Trigger a warning (not a deprecation) - should be captured
        @trigger_error('Test warning', E_USER_WARNING);

        // Check that warning WAS captured
        if ($debugbar->hasCollector('messages')) {
            $messages = $debugbar->getCollector('messages')->collect();
            $finalCount = count($messages['messages']);
            $this->assertGreaterThan($initialCount, $finalCount, 'Non-deprecation errors should still be captured');
        }
    }
}