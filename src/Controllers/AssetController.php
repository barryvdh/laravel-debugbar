<?php namespace Barryvdh\Debugbar\Controllers;

use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

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

    public function js()
    {
        $renderer = $this->debugbar->getJavascriptRenderer();

        $content = $renderer->dumpAssetsToString($renderer->getAssets('js'));

        $response = new Response($content, 200, array(
                'Content-Type' => 'text/javascript',
            ));
        $response->setTtl($this->ttl);

        return $response;
    }

    public function css()
    {
        $renderer = $this->debugbar->getJavascriptRenderer();

        $content = $renderer->dumpAssetsToString($renderer->getAssets('css'));

        $response = new Response($content, 200, array(
                'Content-Type' => 'text/css',
            ));
        $response->setTtl($this->ttl);

        return $response;
    }

}
