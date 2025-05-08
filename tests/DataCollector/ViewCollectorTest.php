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

        debugbar()->boot();

        /** @var \Barryvdh\Debugbar\DataCollector\ViewCollector $collector */
        $collector = debugbar()->getCollector('views');
        $collector->addView(
            view('dashboard')
        );

        tap(Arr::first($collector->collect()['templates']), function (array $template) {
            $this->assertEquals(
                'phpstorm://open?file=' . urlencode(str_replace('\\', '/', realpath(__DIR__ . '/../resources/views/dashboard.blade.php'))) . '&line=1',
                $template['xdebug_link']['url']
            );
        });
    }
}
