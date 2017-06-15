<?php namespace Barryvdh\Debugbar\Middleware;

use Illuminate\Foundation\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Stack implements HttpKernelInterface
{
    /** @var \Symfony\Component\HttpKernel\HttpKernelInterface $kernel */
    protected $kernel;
    /** @var \Illuminate\Foundation\Application $app */
    protected $app;

    /**
     * Create a new debugbar middleware instance
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $kernel
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct(HttpKernelInterface $kernel, Application $app)
    {
        $this->kernel = $kernel;
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        /** @var \Barryvdh\Debugbar\LaravelDebugbar $debugbar */
        $debugbar = $this->app['debugbar'];

        /** @var \Illuminate\Http\Response $response */
        $response = $this->kernel->handle($request, $type, $catch);
        
        return $debugbar->modifyResponse($request, $response);
    }
}
