<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\View\View;
class ViewCollector extends DataCollector  implements Renderable
{

    protected $views = array();

    /**
     * Add a View instance to the Collector
     *
     * @param \Illuminate\View\View $view
     */
    public function addView(View $view){

        $name = $view->getName();
        $data = array();
        foreach($view->getData() as $key => $value)
        {
            if(is_object($value) and method_exists($value, 'toArray'))
            {
                $data[$key] = $value->toArray();
            }else{
                $data[$key] = $value;
            }
        }
        $data = $this->flattenVar($data);
        $this->views[$name] = print_r($data, true);
    }

    /**
     * Flatten a var recursively
     *
     * @param $var
     * @return mixed
     */
    public function flattenVar($var){
        if (is_array($var)) {
            foreach ($var as &$v) {
                $v = $this->flattenVar($v);
            }
        } else if (is_object($var)) {
            $var = "Object(" . get_class($var) . ")";
        }
        return $var;
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $views = $this->views;
        return array(
            'views' => $views
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'views';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return array(
            "views" => array(
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "views.views",
                "default" => "{}"
            )
        );
    }
}
