<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Tests\DataCollector;

use Fruitcake\LaravelDebugbar\Tests\TestCase;
use Fruitcake\LaravelDebugbar\DataCollector\SessionCollector;
use DebugBar\DataFormatter\DataFormatter;
use Illuminate\Session\SymfonySessionDecorator;

class SessionCollectorTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
    }

    public function testItCollectsSessionVariables()
    {
        $collector = new SessionCollector(
            $this->app->make(SymfonySessionDecorator::class),
        );
        $collector->setDataFormatter(new DataFormatter());

        $this->assertEmpty($collector->collect());

        $this->withSession(['testVariable' => "1", 'secret' => 'testSecret'])->get('/');

        $collected = $collector->collect();

        $this->assertNotEmpty($collected);
        $this->assertArrayHasKey('secret', $collected);
        $this->assertArrayHasKey('testVariable', $collected);
        $this->assertEquals('te***et', $collected['secret']);
        $this->assertEquals("1", $collected['testVariable']);

        $this->flushSession();
        $this->assertCount(0, $collector->collect());
    }
}
