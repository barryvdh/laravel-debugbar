<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
class ViewCollector extends DataCollector  implements Renderable
{

    protected $views = array();

    public function addView($view){
        $name = $view->getName();
        $data = array();
        foreach($view->getData() as $key => $value)
        {
            if(method_exists($value, 'toArray'))
            {
                $data[$key] = $value->toArray();
            }else{
                $data[$key] = $value;
            }
        }
        $this->views[$name] = $this->formatVar($data);
    }
    public function collect()
    {
        return $this->views;
    }

    public function getName()
    {
        return 'viewcollector';
    }

    public function getWidgets()
    {
        return array(
            "Views" => array(
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "viewcollector",
                "default" => "{}"
            )
        );
    }
}
