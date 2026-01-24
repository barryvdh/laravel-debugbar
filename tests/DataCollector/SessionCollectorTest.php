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
        $collector = new SessionCollector();
        $collector->setDataFormatter(new DataFormatter());

        static::assertEmpty($collector->collect());

        $this->withSession(['testVariable' => "1", 'secret' => 'testSecret'])->get('/');

        $collected = $collector->collect();

        static::assertNotEmpty($collected);
        static::assertArrayHasKey('secret', $collected);
        static::assertArrayHasKey('testVariable', $collected);
        static::assertEquals('te***et', $collected['secret']);
        static::assertEquals("1", $collected['testVariable']);

        $this->flushSession();
        static::assertCount(0, $collector->collect());
    }
}
