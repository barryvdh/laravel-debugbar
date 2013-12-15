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

        $app = $this->app;
        $debugbar = $this->debugbar;

        $response = $this->app->handle($request, $type, $catch);

        if( $app->runningInConsole() or (!$app['config']->get('laravel-debugbar::config.enabled')) ){
            return $response;
        }

        /** @var \Illuminate\Session\SessionManager $sessionManager */
        $sessionManager = $app['session'];
        $httpDriver = new SymfonyHttpDriver($sessionManager, $response);
        $debugbar->setHttpDriver($httpDriver);

        if($debugbar->shouldCollect('symfony_request', true) and !$debugbar->hasCollector('request')){
            $debugbar->addCollector(new SymfonyRequestCollector($request, $response, $app['session'], $app->make('Symfony\Component\HttpKernel\DataCollector\RequestDataCollector')));
        }

        if($response->isRedirection()){
            $debugbar->stackData();
        }elseif( $request->isXmlHttpRequest() and $app['config']->get('laravel-debugbar::config.capture_ajax', true)){
            $debugbar->sendDataInHeaders();
        }elseif(
            ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
            || 'html' !== $request->getRequestFormat()
        ){
            //Do nothing
        }elseif($app['config']->get('laravel-debugbar::config.inject', true)){
            $debugbar->injectDebugbar($response);
        }
        return $response;
    }
}
