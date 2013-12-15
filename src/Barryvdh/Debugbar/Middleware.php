<?php namespace Barryvdh\Debugbar;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Barryvdh\Debugbar\DataCollector\SymfonyRequestCollector;

class Middleware implements HttpKernelInterface {


    /**
     * Create a new debugbar middleware instance
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $app
     * @param LaravelDebugbar $debugbar
     */
    public function __construct(HttpKernelInterface $app, LaravelDebugbar $debugbar)
    {
        $this->app = $app;
        $this->debugbar = $debugbar;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {

        $response = $this->app->handle($request, $type, $catch);
        return $this->debugbar->modifyResponse($request, $response);
    }
}
