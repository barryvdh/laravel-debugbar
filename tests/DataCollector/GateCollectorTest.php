<?php

namespace Barryvdh\Debugbar\Tests\DataCollector;

use Barryvdh\Debugbar\Tests\Models\User;
use Barryvdh\Debugbar\Tests\TestCase;
use Illuminate\Support\Facades\Gate;

class GateCollectorTest extends TestCase
{
    public function testItCollectsGateChecks()
    {
        debugbar()->boot();

        /** @var \Barryvdh\Debugbar\DataCollector\GateCollector $collector */
        $collector = debugbar()->getCollector('gate');
        $collector->useHtmlVarDumper(false);

        $user = new User([
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $user->can('test');

        Gate::before(function ($user, $ability, $result, $arguments = []) {
            return true;
        });

        $user->can('test');

        $collect = $collector->collect();
        $this->assertEquals(2, $collect['count']);

        $gateError = $collect['messages'][0];
        $this->assertEquals('error', $gateError['label']);
        $this->assertEquals(
            '[ability => test, result => null, user => 1, arguments => []]',
            $gateError['message']
        );

        $gateSuccess = $collect['messages'][1];
        $this->assertEquals('success', $gateSuccess['label']);
        $this->assertEquals(
            '[ability => test, result => true, user => 1, arguments => []]',
            $gateSuccess['message']
        );
    }
}
