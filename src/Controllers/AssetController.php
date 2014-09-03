<?php namespace Barryvdh\Debugbar\Controllers;

use Illuminate\Foundation\Application;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class AssetController extends Controller {

    /** @var int The TTL (1 year) */
    protected $ttl = 31536000;

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

    /**
     * Return the javascript for the Debugbar
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function js()
    {
        $renderer = $this->debugbar->getJavascriptRenderer();

        $content = $renderer->dumpAssetsToString('js');

        $response = new Response($content, 200, array(
                'Content-Type' => 'text/javascript',
            ));
        $response->setTtl($this->ttl);

        return $response;
    }

    /**
     * Return the stylesheets for the Debugbar
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function css()
    {
        $renderer = $this->debugbar->getJavascriptRenderer();

        $content = $renderer->dumpAssetsToString('css');

        $response = new Response($content, 200, array(
                'Content-Type' => 'text/css',
            ));
        $response->setTtl($this->ttl);

        return $response;
    }

}
