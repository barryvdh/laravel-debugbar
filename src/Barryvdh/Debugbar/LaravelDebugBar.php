<?php


namespace Barryvdh\Debugbar;
use DebugBar\DebugBar;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Session\Store;
use DebugBar\DebugBarException;
/**
 * Debug bar subclass which adds all without Request and with LaravelCollector.
 * Rest is added in Service Provider
 */
class LaravelDebugbar extends DebugBar
{


    /** @var  Store $session */
    protected $session = null;
    public function setSessionStore(Store $session){
        $this->session = $session;
    }

    /**
     * Injects the web debug toolbar into the given Response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response A Response instance
     * Source: https://github.com/symfony/WebProfilerBundle/blob/master/EventListener/WebDebugToolbarListener.php
     */
    public function injectDebugbar(Response $response)
    {
        if (function_exists('mb_stripos')) {
            $posrFunction   = 'mb_strripos';
            $substrFunction = 'mb_substr';
        } else {
            $posrFunction   = 'strripos';
            $substrFunction = 'substr';
        }

        $content = $response->getContent();
        $pos = $posrFunction($content, '</body>');

        $renderer = $this->getJavascriptRenderer();
        $debugbar = $renderer->renderHead() . $renderer->render();

        if (false !== $pos) {
            $content = $substrFunction($content, 0, $pos).$debugbar.$substrFunction($content, $pos);
        }else{
            $content = $content . $debugbar;
        }

        $response->setContent($content);
    }


}