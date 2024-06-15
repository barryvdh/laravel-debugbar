<?php

namespace Barryvdh\Debugbar\Tests\Mocks;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\View\Component;

class MockController extends Controller
{
    public function show()
    {
        return view('dashboard');
    }
}