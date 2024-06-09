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
            ['data' => [
                User::class => [
                    'value' => 1,
                    'xdebug_link' => [
                        'url' => 'vscode://file/' . urlencode(str_replace('\\', '/', realpath(__DIR__ . '/../Models/User.php'))) . ':1',
                        'ajax' => false,
                        'filename' => 'User.php',
                        'line' => '?',
                    ],
                ]
            ],
            'count' => 1,
            'is_counter' => true
            ],
            $collector->collect()
        );

        Person::all();

        $this->assertEquals(
            ['data' => [
                User::class => [
                    'value' => 1,
                    'xdebug_link' => [
                        'url' => 'vscode://file/' . urlencode(str_replace('\\', '/', realpath(__DIR__ . '/../Models/User.php'))) . ':1',
                        'ajax' => false,
                        'filename' => 'User.php',
                        'line' => '?',
                    ],
                ],
                Person::class => [
                    'value' => 2,
                    'xdebug_link' => [
                        'url' => 'vscode://file/' . urlencode(str_replace('\\', '/', realpath(__DIR__ . '/../Models/Person.php'))) . ':1',
                        'ajax' => false,
                        'filename' => 'Person.php',
                        'line' => '?',
                    ],
                ],
            ],
            'count' => 3,
            'is_counter' => true
            ],
            $collector->collect()
        );
    }
}
