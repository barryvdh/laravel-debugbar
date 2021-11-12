<?php

namespace Barryvdh\Debugbar\Tests\DataCollector;

use Barryvdh\Debugbar\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;

class ViewCollectorTest extends TestCase
{
    use RefreshDatabase;

    public function testIdeLinksAreAbsolutePaths()
    {
         if (!ini_get('xdebug.file_link_format')) {
            $this->markTestSkipped(
              'The Xdebug extension is not available.'
            );
            return;
        }
        
        debugbar()->boot();

        /** @var \Barryvdh\Debugbar\DataCollector\ViewCollector $collector */
        $collector = debugbar()->getCollector('views');
        $collector->addView(
            view('dashboard')
        );

        tap(Arr::first($collector->collect()['templates']), function (array $template) {
            $this->assertEquals(
                'vscode://file/' . realpath(__DIR__ . '/../resources/views/dashboard.blade.php') . ':1',
                $template['xdebug_link']['url']
            );
        });
    }
}
