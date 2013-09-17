<?php


namespace Barryvdh\Debugbar;
use DebugBar\DebugBar;
use Symfony\Component\HttpFoundation\Response;

/**
 * Debug bar subclass which adds all without Request and with LaravelCollector.
 * Rest is added in Service Provider
 */
class LaravelDebugbar extends DebugBar
{

    /**
     * Add the data to headers, for Ajax requests
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param string $headerName
     * @param int $maxHeaderLength
     */
    public function addDataToHeaders($response, $headerName = 'phpdebugbar', $maxHeaderLength = 4096)
    {
        $headers = array();
        $data = rawurlencode(json_encode(array(
                    'id' => $this->getCurrentRequestId(),
                    'data' => $this->getData()
                )));
        $chunks = array();

        while (strlen($data) > $maxHeaderLength) {
            $chunks[] = substr($data, 0, $maxHeaderLength);
            $data = substr($data, $maxHeaderLength);
        }
        $chunks[] = $data;

        for ($i = 0, $c = count($chunks); $i < $c; $i++) {
            $name = $headerName . ($i > 0 ? "-$i" : '');
            $headers[$name] = $chunks[$i];

        }

        $response->headers->add($headers);
    }

    /**
     * Injects the web debug toolbar into the given Response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response A Response instance
     * Source: https://github.com/symfony/WebProfilerBundle/blob/master/EventListener/WebDebugToolbarListener.php
     */
    public function injectDebugbar($response)
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