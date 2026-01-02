<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\Tests\Mocks;

use Illuminate\View\InvokableComponentVariable;

class MockViewComponent extends InvokableComponentVariable
{
    public function render()
    {
        return view('dashboard');
    }
}
