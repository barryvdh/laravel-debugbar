<?php namespace Barryvdh\Debugbar\Controllers;

use Barryvdh\Debugbar\LaravelDebugbar;
use Illuminate\Routing\Controller;

if (class_exists('Illuminate\Routing\Controller')) {

    class BaseController extends Controller
    {
        protected $debugbar;

        public function __construct(LaravelDebugbar $debugbar)
        {
            $this->debugbar = $debugbar;
        }
    }

} else {

    class BaseController
    {
        protected $debugbar;

        public function __construct(LaravelDebugbar $debugbar)
        {
            $this->debugbar = $debugbar;
        }
    }
}

