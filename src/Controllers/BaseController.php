<?php

namespace Barryvdh\Debugbar\Controllers;

use Barryvdh\Debugbar\LaravelDebugbar;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Laravel\Telescope\Telescope;

// phpcs:ignoreFile
if (class_exists('Illuminate\Routing\Controller')) {

    class BaseController extends Controller
    {
        protected $debugbar;

        public function __construct(Request $request, LaravelDebugbar $debugbar)
        {
            $this->debugbar = $debugbar;

            if ($request->hasSession()) {
                $request->session()->reflash();
            }

            $this->middleware(function ($request, $next) {
                if (class_exists(Telescope::class)) {
                    Telescope::stopRecording();
                }
                return $next($request);
            });
        }
    }

} else {

    class BaseController
    {
        protected $debugbar;

        public function __construct(Request $request, LaravelDebugbar $debugbar)
        {
            $this->debugbar = $debugbar;

            if ($request->hasSession()) {
                $request->session()->reflash();
            }
        }
    }
}
