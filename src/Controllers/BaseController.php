<?php namespace Barryvdh\Debugbar\Controllers;

use Barryvdh\Debugbar\LaravelDebugbar;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    /**
     * The laravel debugbar instance.
     *
     * @var \Barryvdh\Debugbar\LaravelDebugbar
     */
    protected $debugbar;

    /**
     * Create a new controller instance.
     *
     * @param \Barryvdh\Debugbar\LaravelDebugbar $debugbar
     *
     * @return void
     */
    public function __construct(LaravelDebugbar $debugbar)
    {
        $this->debugbar = $debugbar;
    }
}
