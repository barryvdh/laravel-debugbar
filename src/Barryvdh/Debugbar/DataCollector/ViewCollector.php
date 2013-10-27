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

        $this->views[] = $name . ' => ' . $this->formatVar($data);
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
        }else{
            $var = htmlentities($var, ENT_QUOTES, 'UTF-8', false);
        }
        return $var;
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $views = $this->views;
        $messages = array();
        foreach($views as $data){
            $messages[] = array(
                'message' => $data,
                'is_string' => true,
            );
        }
        return array('messages' => $messages);
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
                "widget" => "PhpDebugBar.Widgets.MessagesWidget",
                "map" => "views.messages",
                "default" => "{}"
            )
        );
    }
}
