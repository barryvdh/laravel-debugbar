<?php namespace Barryvdh\Debugbar\Controllers;

use Barryvdh\Debugbar\LaravelDebugbar;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

if (class_exists('Illuminate\Routing\Controller')) {

    class BaseController extends Controller
    {
        protected $debugbar;

        public function __construct(Request $request, LaravelDebugbar $debugbar)
        {
            $this->debugbar = $debugbar;

            if ($request->hasSession()){
                $request->session()->reflash();
            }
        }
    }

} else {

    class BaseController
    {
        protected $debugbar;

        public function __construct(Request $request, LaravelDebugbar $debugbar)
        {
            $this->debugbar = $debugbar;

            if ($request->hasSession()){
                $request->session()->reflash();
            }
        }
    }
}
