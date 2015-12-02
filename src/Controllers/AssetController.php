<?php namespace Barryvdh\Debugbar\Controllers;

use Illuminate\Http\Response;

class AssetController extends BaseController
{
    /**
     * Return the javascript for the Debugbar
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function js()
    {
        $renderer = $this->debugbar->getJavascriptRenderer();

        $content = $renderer->dumpAssetsToString('js');

        $response = new Response(
            $content, 200, array(
                'Content-Type' => 'text/javascript',
            )
        );

        return $this->cacheResponse($response);
    }

    /**
     * Return the Debugbar javascript as a module for use with requirejs
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function module()
    {
        $content = file_get_contents(__DIR__ . "/../Resources/debugbar-all.js");

        $response = new Response(
            $content, 200, array(
                'Content-Type' => 'text/javascript',
            )
        );

        return $this->cacheResponse($response);
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

        $response = new Response(
            $content, 200, array(
                'Content-Type' => 'text/css',
            )
        );

        return $this->cacheResponse($response);
    }

    /**
     * Cache the response 1 year (31536000 sec)
     */
    protected function cacheResponse(Response $response)
    {
        $response->setSharedMaxAge(31536000);
        $response->setMaxAge(31536000);
        $response->setExpires(new \DateTime('+1 year'));

        return $response;
    }
}
