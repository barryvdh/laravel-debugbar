<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\CollectorProviders;

use Fruitcake\LaravelDebugbar\DataCollector\LivewireCollector;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\Livewire;

class LivewireCollectorProvider extends AbstractCollectorProvider
{
    public function __invoke(Application $app, Request $request): void
    {
        if ($app->bound('livewire')) {

            $livewireCollector = new LivewireCollector(true, [], false);
            $this->addCollector($livewireCollector);

            // Livewire 2.x
            Livewire::listen('view:render', fn(View $view) => $livewireCollector->addLivewire2View($view, $request));

            // Livewire 3.x/4.x
            Livewire::listen('render', fn(Component $component) => $livewireCollector->addLivewireComponent($component, $request));
        }
    }
}
