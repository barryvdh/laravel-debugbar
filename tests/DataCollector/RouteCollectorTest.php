<?php

namespace Barryvdh\Debugbar\Tests\DataCollector;

use Barryvdh\Debugbar\Tests\TestCase;
use Barryvdh\Debugbar\DataCollector\RouteCollector;
use DebugBar\DataCollector\DataCollector;
use PHPUnit\Framework\Attributes\DataProvider;


class RouteCollectorTest extends TestCase
{
    /** @var \Barryvdh\Debugbar\DataCollector\RouteCollector $collector */
    private DataCollector $routeCollector;
    protected function setUp(): void
    {
        parent::setUp();

        debugbar()->boot();
        $this->routeCollector = debugbar()->getCollector('route');
        $this->routeCollector->setEditorLinkTemplate('vscode');
    }

    public function testItCollectsRouteUri()
    {
        $this->get('web/html');

        $this->assertSame('GET web/html', $this->routeCollector->collect()['uri']);

        $this->call('POST','web/mw');

        $this->assertSame('POST web/mw', $this->routeCollector->collect()['uri']);
    }

    #[DataProvider('controllerProvider')]
    public function testItCollectsWithControllerHandler($controller, $file)
    {
        $this->get('web/show');

        $collected = $this->routeCollector->collect();

        $this->assertCount(3, $collected);
        $this->assertArrayHasKey('controller', $collected);
        $this->assertArrayHasKey('file', $collected);
        $this->assertEquals($controller, $collected['controller']);
        $this->assertStringContainsString($file, $collected['file']);
    }

    #[DataProvider('viewComponentProvider')]
    public function testItCollectsWithViewComponentHandler($controller, $file)
    {
        $this->get('web/view');

        $collected = $this->routeCollector->collect();

        $this->assertEquals($controller, $collected['controller']);
        $this->assertStringContainsString($file, $collected['file']);
    }

    #[DataProvider('closureProvider')]
    public function testItCollectsWithClosureHandler($controller, $file)
    {
        $this->get('web/html');

        $collected = $this->routeCollector->collect();

        $this->assertCount(3, $collected);
        $this->assertArrayHasKey('uses', $collected);
        $this->assertArrayHasKey('file', $collected);
        $this->assertStringContainsString($controller, $collected['uses']);
        $this->assertStringContainsString($file, $collected['file']);
    }

    public function testItCollectsMiddleware()
    {
        $this->call('POST','web/mw');

        $collected = $this->routeCollector->collect();

        $this->assertCount(4, $collected);
        $this->assertArrayHasKey('middleware', $collected);
        $this->assertSame('Barryvdh\Debugbar\Tests\Mocks\MockMiddleware', $collected['middleware']);

    }

    public static function controllerProvider()
    {
        $filePath = urlencode(str_replace('\\', '/', realpath(__DIR__ . '/../Mocks/MockController.php')));
        return [['Barryvdh\Debugbar\Tests\Mocks\MockController@show',
                 sprintf('vscode://file/%s:', $filePath)
        ]];
    }

    public static function viewComponentProvider()
    {
        $filePath = urlencode(str_replace('\\', '/', realpath(__DIR__ . '/../Mocks/MockViewComponent.php')));
        return [['Barryvdh\Debugbar\Tests\Mocks\MockViewComponent@render',
                 sprintf('vscode://file/%s:', $filePath)
        ]];
    }

    public static function closureProvider()
    {
        $filePath = realpath(__DIR__ . '/../TestCase.php');
        return [[sprintf('file: "%s"', $filePath),
                 sprintf('vscode://file/%s:', urlencode(str_replace('\\', '/', $filePath)))
        ]];
    }
}