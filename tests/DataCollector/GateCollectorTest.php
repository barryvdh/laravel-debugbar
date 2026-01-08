<?php

declare(strict_types=1);

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

        $user->can('view', $user);

        Gate::before(function ($user, $ability, $result, $arguments = []) {
            return true;
        });

        $user->can('view', $user);

        $collect = $collector->collect();
        $this->assertEquals(2, $collect['count']);

        $gateError = $collect['messages'][0];
        $this->assertEquals('error', $gateError['label']);
        $this->assertEquals(
            'view Barryvdh\Debugbar\Tests\Models\User(id=1)',
            $gateError['message'],
        );
        $this->assertEquals(
            $gateError['context'],
            [
                'ability' => 'view',
                'target' => 'Barryvdh\Debugbar\Tests\Models\User(id=1)',
                'result' => null,
                'user' => 1,
                'arguments' => [
                    $user,
                ],
            ],
        );

        $gateSuccess = $collect['messages'][1];
        $this->assertEquals('success', $gateSuccess['label']);
        $this->assertEquals(
            'view Barryvdh\Debugbar\Tests\Models\User(id=1)',
            $gateSuccess['message'],
        );
        $this->assertEquals(
            $gateSuccess['context'],
            [
                'ability' => 'view',
                'target' => 'Barryvdh\Debugbar\Tests\Models\User(id=1)',
                'result' => true,
                'user' => 1,
                'arguments' => [
                    $user,
                ],
            ],
        );
    }
}
