<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Tests\DataCollector\Livewire;

use Livewire\Component;

class DummyComponent extends Component
{
    public string $title = 'MyComponent';

    public function render()
    {
        return <<<'blade'
                <div>
                    Hello!
                </div>
            blade;
    }
}
