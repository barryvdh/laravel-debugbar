<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\Tests\DataCollector;

use Barryvdh\Debugbar\Tests\TestCase;
use Barryvdh\Debugbar\DataCollector\SessionCollector;
use Illuminate\Session\SessionManager;

class SessionCollectorTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('debugbar.options.session.hiddens', ['secret']);
        parent::getEnvironmentSetUp($app);
    }

    public function testItCollectsSessionVariables()
    {
        /** @var \Barryvdh\Debugbar\DataCollector\SessionCollector $collector */
        $collector = new SessionCollector(
            $this->app->make(SessionManager::class),
            $this->app['config']->get('debugbar.options.session.hiddens', []),
        );

        $this->assertEmpty($collector->collect());

        $this->withSession(['testVariable' => 1, 'secret' => 'testSecret'])->get('/');

        $collected = $collector->collect();

        $this->assertNotEmpty($collected);
        $this->assertArrayHasKey('secret', $collected);
        $this->assertArrayHasKey('testVariable', $collected);
        $this->assertEquals($collected['secret'], '******');
        $this->assertEquals($collected['testVariable'], 1);

        $this->flushSession();
        $this->assertCount(0, $collector->collect());
    }
}
