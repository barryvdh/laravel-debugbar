<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Tests\DataCollector;

use Fruitcake\LaravelDebugbar\Tests\Models\User;
use Fruitcake\LaravelDebugbar\Tests\TestCase;
use DebugBar\DataFormatter\DataFormatter;
use Illuminate\Support\Facades\Gate;

class GateCollectorTest extends TestCase
{
    public function testItCollectsGateChecks()
    {
        debugbar()->boot();

        /** @var \Fruitcake\LaravelDebugbar\DataCollector\GateCollector $collector */
        $collector = debugbar()->getCollector('gate');
        $collector->setDataFormatter(new DataFormatter());

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
            'view Fruitcake\LaravelDebugbar\Tests\Models\User(id=1)',
            $gateError['message'],
        );
        $this->assertEquals(
            [
                'ability' => '"view"',
                'target' => '"Fruitcake\LaravelDebugbar\Tests\Models\User(id=1)"',
                'result' => 'null',
                'user' => '1',
                'arguments' => 'array:1 [
  0 => "Fruitcake\LaravelDebugbar\Tests\Models\User(id=1)"
]',
            ],
            $gateError['context']
        );

        $gateSuccess = $collect['messages'][1];
        $this->assertEquals('success', $gateSuccess['label']);
        $this->assertEquals(
            'view Fruitcake\LaravelDebugbar\Tests\Models\User(id=1)',
            $gateSuccess['message'],
        );
        $this->assertEquals(
            $gateSuccess['context'],
            [
                'ability' => '"view"',
                'target' => '"Fruitcake\LaravelDebugbar\Tests\Models\User(id=1)"',
                'result' => 'true',
                'user' => '1',
                'arguments' => 'array:1 [
  0 => "Fruitcake\LaravelDebugbar\Tests\Models\User(id=1)"
]',
            ],
        );
    }
}
