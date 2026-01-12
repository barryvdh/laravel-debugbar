<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Controllers;

use Fruitcake\LaravelDebugbar\LaravelDebugbar;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Laravel\Telescope\Telescope;

class BaseController extends Controller
{
    protected $debugbar;

    public function __construct(Request $request, LaravelDebugbar $debugbar)
    {
        $this->debugbar = $debugbar;

        if ($request->hasSession()) {
            /** @var Store $session */
            $session = $request->session();
            $session->reflash();
        }

        $this->middleware(function ($request, $next) {
            if (class_exists(Telescope::class)) {
                Telescope::stopRecording();
            }
            return $next($request);
        });
    }
}
