<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\Tests\Mocks;

use Illuminate\Routing\Controller;

class MockController extends Controller
{
    public function show()
    {
        return view('dashboard');
    }
}
