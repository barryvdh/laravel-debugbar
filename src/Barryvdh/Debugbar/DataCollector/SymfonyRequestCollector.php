<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use DebugBar\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\RequestDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SymfonyRequestCollector extends DataCollector implements DataCollectorInterface, Renderable
{

    /** @var \Symfony\Component\HttpFoundation\Request $request */
    protected $request;
    /** @var  \Symfony\Component\HttpFoundation\Request $response */
    protected $response;
    /** @var  \Symfony\Component\HttpFoundation\Session\SessionInterface $session */
    protected $session;
    /** @var  \Symfony\Component\HttpKernel\DataCollector\RequestDataCollector $collector */
    protected $collector;


    /**
     * Create a new SymfonyRequestCollector
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Request $response
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param \Symfony\Component\HttpKernel\DataCollector\RequestDataCollector $collector
     */
    public function __construct($request, $response, $session, RequestDataCollector $collector)
    {
        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
        $this->collector = $collector;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(){
        $this->collector->collect($this->request, $this->response);
        $data = unserialize($this->collector->serialize());

        $session_attributes = array();
        foreach($this->session->all() as $key => $value){
            $session_attributes[$key] = $value;
        }

        $data['session_attributes'] = $session_attributes;
        foreach($data as $key => $var){
            if(empty($data[$key])){
                $data[$key] = '-';
            }else{
                $data[$key] = $this->formatVar($var);
            }

        }
        unset($data['content']);
        unset($data['controller']);
        unset($data['session_metadata']);



        $data['locale'] = \Config::get('app.locale', '-');

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'request';
    }
    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return array(
            "request" => array(
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "request",
                "default" => "{}"
            )
        );
    }
}
