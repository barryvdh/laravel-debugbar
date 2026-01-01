<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\Tests\DataCollector;

use Barryvdh\Debugbar\Tests\TestCase;
use DebugBar\DataCollector\DataCollector;

class RouteCollectorTest extends TestCase
{
    /** @var \Barryvdh\Debugbar\DataCollector\RouteCollector $collector */
    private DataCollector $routeCollector;
    protected function setUp(): void
    {
        parent::setUp();
        debugbar()->boot();

        $this->routeCollector = debugbar()->getCollector('route');
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('debugbar.collectors.route', true);

        parent::getEnvironmentSetUp($app);
    }

    public function testItCollectsRouteUri()
    {
        $this->get('web/html');
        $this->assertSame('GET web/html', $this->routeCollector->collect()['uri']);

        $this->call('POST', 'web/mw');
        $this->assertSame('POST web/mw', $this->routeCollector->collect()['uri']);
    }

    /**
     * @dataProvider controllerData
     */
    public function testItCollectsWithControllerHandler($controller, $file, $url)
    {
        $this->get('web/show');

        $collected = $this->routeCollector->collect();

        $this->assertNotEmpty($collected);
        $this->assertArrayHasKey('file', $collected);
        $this->assertArrayHasKey('controller', $collected);
        $this->assertStringContainsString($file, $collected['file']['value']);
        $this->assertStringContainsString($url, $collected['file']['xdebug_link']['url']);
        $this->assertStringContainsString($controller, $collected['controller']['value']);
        $this->assertStringContainsString($url, $collected['controller']['xdebug_link']['url']);
    }

    /**
     * @dataProvider viewComponentData
     */
    public function testItCollectsWithViewComponentHandler($controller, $file, $url)
    {
        $this->get('web/view');

        $collected = $this->routeCollector->collect();

        $this->assertStringContainsString($file, $collected['file']['value']);
        $this->assertStringContainsString($url, $collected['file']['xdebug_link']['url']);
        $this->assertStringContainsString($controller, $collected['controller']['value']);
        $this->assertStringContainsString($url, $collected['controller']['xdebug_link']['url']);
    }

    /**
     * @dataProvider closureData
     */
    public function testItCollectsWithClosureHandler($file)
    {
        $this->get('web/html');

        $collected = $this->routeCollector->collect();

        $this->assertNotEmpty($collected);
        $this->assertArrayHasKey('uses', $collected);
        $this->assertArrayHasKey('file', $collected);
        $this->assertStringContainsString($file, $collected['uses']);
        $this->assertStringContainsString($file, $collected['file']['value']);
    }

    public function testItCollectsMiddleware()
    {
        $this->call('POST', 'web/mw');

        $collected = $this->routeCollector->collect();

        $this->assertNotEmpty($collected);
        $this->assertArrayHasKey('middleware', $collected);
        $this->assertStringContainsString('MockMiddleware', $collected['middleware']);
    }

    public static function controllerData()
    {
        $filePath = urlencode(str_replace('\\', '/', realpath(__DIR__ . '/../Mocks/MockController.php')));
        return [['MockController@show',
            'MockController.php',
            sprintf('phpstorm://open?file=%s', $filePath),
        ]];
    }

    public static function viewComponentData()
    {
        $filePath = urlencode(str_replace('\\', '/', realpath(__DIR__ . '/../Mocks/MockViewComponent.php')));
        return [['MockViewComponent@render',
            'MockViewComponent.php',
            sprintf('phpstorm://open?file=%s', $filePath),
        ]];
    }

    public static function closureData()
    {
        return [['TestCase.php']];
    }
}
