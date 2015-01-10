<?php namespace Barryvdh\Debugbar\Controllers;

use Barryvdh\Debugbar\LaravelDebugbar;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    /**
     * The application instance.
     *
     * @var \Barryvdh\Debugbar\LaravelDebugbar
     */
    protected $debugbar;

    public function __construct(LaravelDebugbar $debugbar)
    {
        $this->debugbar = $debugbar;
    }
}
