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
        $collector->collectCountSummary(false);
        $collector->setKeyMap([]);
        $data = [];

        $this->assertEquals(
            ['data' => $data, 'key_map' => [], 'count' => 0, 'is_counter' => true],
            $collector->collect(),
        );

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

        $data[User::class] = ['created' => 2];
        $this->assertEquals(
            [
                'data' => $data,
                'count' => 2,
                'is_counter' => true,
                'key_map' => [
                ],
            ],
            $collector->collect(),
        );

        $user = User::first();

        $data[User::class]['retrieved'] = 1;
        $this->assertEquals(
            ['data' => $data, 'key_map' => [], 'count' => 3, 'is_counter' => true],
            $collector->collect(),
        );

        $user->update(['name' => 'Jane Doe']);

        $data[User::class]['updated'] = 1;
        $this->assertEquals(
            [
                'data' => $data,
                'count' => 4,
                'is_counter' => true,
                'key_map' => [],
            ],
            $collector->collect(),
        );

        Person::all();

        $data[Person::class] = ['retrieved' => 2];
        $this->assertEquals(
            ['data' => $data, 'key_map' => [], 'count' => 6, 'is_counter' => true],
            $collector->collect(),
        );

        $user->delete();

        $data[User::class]['deleted'] = 1;
        $this->assertEquals(
            [
                'data' => $data,
                'count' => 7,
                'is_counter' => true,
                'key_map' => [
                ],
            ],
            $collector->collect(),
        );
    }
}
