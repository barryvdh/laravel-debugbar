<?php namespace Barryvdh\Debugbar\Controllers;

use Symfony\Component\HttpFoundation\Response;

class AssetController extends BaseController
{

    /** @var int The TTL (1 year) */
    protected $ttl = 31536000;

    /**
     * Return the javascript for the Debugbar
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function js()
    {
        $renderer = $this->app['debugbar']->getJavascriptRenderer();

        $content = $renderer->dumpAssetsToString('js');

        $response = new Response(
            $content, 200, array(
                'Content-Type' => 'text/javascript',
            )
        );
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
        $renderer = $this->app['debugbar']->getJavascriptRenderer();

        $content = $renderer->dumpAssetsToString('css');

        $response = new Response(
            $content, 200, array(
                'Content-Type' => 'text/css',
            )
        );
        $response->setTtl($this->ttl);

        return $response;
    }

}
