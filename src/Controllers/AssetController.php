<?php namespace Barryvdh\Debugbar\Controllers;

use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

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

        ob_start();
        $renderer->dumpJsAssets();
        $content = ob_get_contents();

        return new Response($content, 200, array(
                'Content-Type' => 'text/javascript',
            ));
    }

    public function css()
    {
        $renderer = $this->debugbar->getJavascriptRenderer();

        ob_start();
        $renderer->dumpCssAssets();
        $content = ob_get_contents();

        return new Response($content, 200, array(
                'Content-Type' => 'text/css',
            ));
    }

}
