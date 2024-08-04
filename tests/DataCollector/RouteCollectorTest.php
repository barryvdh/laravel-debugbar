<?php

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
    public function testItCollectsWithControllerHandler($controller, $file)
    {
        $this->get('web/show');

        $collected = $this->routeCollector->collect();

        $this->assertNotEmpty($collected);
        $this->assertArrayHasKey('file', $collected);
        $this->assertArrayHasKey('controller', $collected);
        $this->assertStringContainsString($file, $collected['file']);
        $this->assertStringContainsString($controller, $collected['controller']);
    }

    /**
     * @dataProvider viewComponentData
     */
    public function testItCollectsWithViewComponentHandler($controller, $file)
    {
        $this->get('web/view');

        $collected = $this->routeCollector->collect();

        $this->assertStringContainsString($file, $collected['file']);
        $this->assertStringContainsString($controller, $collected['controller']);
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
        $this->assertStringContainsString($file, $collected['file']);
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
                 sprintf('phpstorm://open?file=%s', $filePath)
        ]];
    }

    public static function viewComponentData()
    {
        $filePath = urlencode(str_replace('\\', '/', realpath(__DIR__ . '/../Mocks/MockViewComponent.php')));
        return [['MockViewComponent@render',
                 sprintf('phpstorm://open?file=%s', $filePath)
        ]];
    }

    public static function closureData()
    {
        return [['TestCase.php']];
    }
}
