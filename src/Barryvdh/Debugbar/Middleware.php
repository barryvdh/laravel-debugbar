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
     * Handles a Request to convert it to a Response.
     *
     * When $catch is true, the implementation must catch all exceptions
     * and do its best to convert them to a Response instance.
     *
     * @param Request $request A Request instance
     * @param integer $type The type of the request
     *                          (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param Boolean $catch Whether to catch exceptions or not
     *
     * @return Response A Response instance
     *
     * @throws \Exception When an Exception occurs during processing
     *
     * @api
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {

        $response = $this->app->handle($request, $type, $catch);
        return $this->debugbar->modifyResponse($request, $response);
    }
}
