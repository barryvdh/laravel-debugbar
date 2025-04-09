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
        $eventList = ['retrieved', 'created', 'updated', 'deleted', 'restored'];
        $keyMap = array_combine($eventList, array_map('ucfirst', $eventList));
        $data = [];

        $this->loadLaravelMigrations();

        debugbar()->boot();

        /** @var \DebugBar\DataCollector\ObjectCountCollector $collector */
        $collector = debugbar()->getCollector('models');
        $collector->setXdebugLinkTemplate('');

        $this->assertEquals(
            ['data' => $data, 'count' => 0, 'key_map' => $keyMap, 'is_counter' => true],
            $collector->collect()
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
            ['data' => $data, 'key_map' => $keyMap, 'count' => 2, 'is_counter' => true],
            $collector->collect()
        );

        $user = User::first();

        $data[User::class]['retrieved'] = 1;
        $this->assertEquals(
            ['data' => $data, 'key_map' => $keyMap, 'count' => 3, 'is_counter' => true],
            $collector->collect()
        );

        $user->update(['name' => 'Jane Doe']);

        $data[User::class]['updated'] = 1;
        $this->assertEquals(
            ['data' => $data, 'key_map' => $keyMap, 'count' => 4, 'is_counter' => true],
            $collector->collect()
        );

        Person::all();

        $data[Person::class] = ['retrieved' => 2];
        $this->assertEquals(
            ['data' => $data, 'key_map' => $keyMap, 'count' => 6, 'is_counter' => true],
            $collector->collect()
        );

        $user->delete();

        $data[User::class]['deleted'] = 1;
        $this->assertEquals(
            ['data' => $data, 'key_map' => $keyMap, 'count' => 7, 'is_counter' => true],
            $collector->collect()
        );
    }
}
