<?php

namespace Barryvdh\Debugbar\Tests\Mocks;

use Illuminate\Routing\Controller;

class MockController extends Controller
{
    public function show()
    {
        return view('dashboard');
    }
}
