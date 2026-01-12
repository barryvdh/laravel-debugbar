<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Tests\Mocks;

use Illuminate\Routing\Controller;

class MockController extends Controller
{
    public function show()
    {
        return view('dashboard');
    }
}
