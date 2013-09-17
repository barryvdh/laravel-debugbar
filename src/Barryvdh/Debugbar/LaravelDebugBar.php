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
     * Add the data to headers, for Ajax requests
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param string $headerName
     * @param int $maxHeaderLength
     */
    public function addDataToHeaders(Response $response, $headerName = 'phpdebugbar', $maxHeaderLength = 4096)
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

    /**
     * Stacks the data in the session for later rendering
     */
    public function stackData()
    {
        $this->initStackSession();

        $data = null;
        if (!$this->isDataPersisted() || $this->stackAlwaysUseSessionStorage) {
            $data = $this->getData();
        } else if ($this->data === null) {
            $this->collect();
        }
        $stackedData = $this->session->get($this->stackSessionNamespace);
        $stackedData[$this->getCurrentRequestId()] = $data;
        $this->session->set($this->stackSessionNamespace, $stackedData);
        return $this;
    }


    /**
     * Checks if there is stacked data in the session
     *
     * @return boolean
     */
    public function hasStackedData()
    {
        $this->initStackSession();
        return count($this->session->get($this->stackSessionNamespace)) > 0;
    }

    /**
     * Returns the data stacked in the session
     *
     * @param boolean $delete Whether to delete the data in the session
     * @return array
     */
    public function getStackedData($delete = true)
    {
        $this->initStackSession();
        $stackedData = $this->session->get($this->stackSessionNamespace);
        if ($delete) {
            $this->session->remove($this->stackSessionNamespace);
        }

        $datasets = array();
        if ($this->isDataPersisted() && !$this->stackAlwaysUseSessionStorage) {
            foreach ($stackedData as $id => $data) {
                $datasets[$id] = $this->getStorage()->get($id);
            }
        } else {
            $datasets = $stackedData;
        }

        return $datasets;
    }

    /**
     * Initializes the session for stacked data
     */
    protected function initStackSession()
    {
        if (!isset($this->session)) {
            throw new DebugBarException("Session must be started before using stack data in the debug bar");
        }

        $ns = $this->stackSessionNamespace;
        if (!$this->session->has($ns)) {
            $this->session->set($ns, array());
        }
    }

}