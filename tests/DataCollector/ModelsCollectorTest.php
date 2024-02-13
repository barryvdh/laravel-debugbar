<?php

namespace Barryvdh\Debugbar\Tests\DataCollector;

use Barryvdh\Debugbar\Tests\Models\Person;
use Barryvdh\Debugbar\Tests\Models\User;
use Barryvdh\Debugbar\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ModelsCollectorTest extends TestCase
{
    use RefreshDatabase;

    public function testItCollectsRetrievedModels()
    {
        $this->loadLaravelMigrations();

        debugbar()->boot();

        /** @var \DebugBar\DataCollector\ObjectCountCollector $collector */
        $collector = debugbar()->getCollector('models');
        $collector->setXdebugLinkTemplate('');

        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->assertEquals(
            ['data' => [], 'count' => 0, 'is_counter' => true],
            $collector->collect()
        );

        User::first();

        $this->assertEquals(
            ['data' => [User::class => 1], 'count' => 1, 'is_counter' => true],
            $collector->collect()
        );

        Person::all();

        $this->assertEquals(
            ['data' => [User::class => 1, Person::class => 2], 'count' => 3, 'is_counter' => true],
            $collector->collect()
        );
    }
}
