<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Tests\DataCollector\Livewire;

use Livewire\Component;

class DummyComponent extends Component
{
    public string $title = 'MyComponent';

    public int $counter = 1;

    public function increment()
    {
        $this->counter++;
    }

    public function render()
    {
        return <<<'blade'
                <div>
                    Hello. You are #{{ $counter }}!
                    <br/>
                    <a wire:click="increment">Increase</a>
                </div>
            blade;
    }
}
