<?php namespace Barryvdh\Debugbar\Controllers;

use Illuminate\Foundation\Application;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetController extends Controller {

    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The debugbar instance.
     *
     * @var \Barryvdh\Debugbar\LaravelDebugbar
     */
    protected $debugbar;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->debugbar = $this->app['debugbar'];
    }

    public function js()
    {
        $renderer = $this->debugbar->getJavascriptRenderer();

        return new StreamedResponse(function() use($renderer){
                $renderer->dumpJsAssets();
            }, 200, array(
                'Content-Type' => 'text/javascript',
            ));
    }

    public function css()
    {
        $renderer = $this->debugbar->getJavascriptRenderer();

        return new StreamedResponse(function() use($renderer){
                $renderer->dumpCssAssets();
            }, 200, array(
                'Content-Type' => 'text/css',
            ));
    }

}
