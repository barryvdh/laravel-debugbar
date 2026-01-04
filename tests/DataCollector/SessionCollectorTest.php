<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\Tests\DataCollector;

use Barryvdh\Debugbar\Tests\TestCase;
use Barryvdh\Debugbar\DataCollector\SessionCollector;
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

        $this->assertEmpty($collector->collect());

        $this->withSession(['testVariable' => 1, 'secret' => 'testSecret'])->get('/');

        $collected = $collector->collect();

        $this->assertNotEmpty($collected);
        $this->assertArrayHasKey('secret', $collected);
        $this->assertArrayHasKey('testVariable', $collected);
        $this->assertEquals('te***et', $collected['secret']);
        $this->assertEquals(1, $collected['testVariable']);

        $this->flushSession();
        $this->assertCount(0, $collector->collect());
    }
}
