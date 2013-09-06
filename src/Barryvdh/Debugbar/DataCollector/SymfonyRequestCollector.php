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

    protected $request;
    protected $response;
    protected $collector;


    public function __construct(Request $request, Response $response, RequestDataCollector $collector)
    {
        $this->request = $request;
        $this->response = $response;
        $this->collector = $collector;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(){
        $this->collector->collect($this->request, $this->response);
        $data = unserialize($this->collector->serialize());
        foreach($data as $key => $var){
            $data[$key] = $this->formatVar($var);
        }
        unset($data['content']);
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
