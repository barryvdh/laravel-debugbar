<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\ConfigCollector;
use Illuminate\View\View;
class ViewCollector extends ConfigCollector
{

    protected $views = array();
    protected $collect_data;

    /**
     * Create a ViewCollector
     *
     * @param bool $collectData  Collects view data when tru
     */
    public function __construct($collectData = true) {
        $this->collect_data = $collectData;
        $this->name = 'views';
        $this->data = array();
    }

    /**
     * Add a View instance to the Collector
     *
     * @param \Illuminate\View\View $view
     */
    public function addView(View $view) {
        $name = $view->getName();
        if(!$this->collect_data){
            $this->data[$name] = $name;
        }else{
            $data = array();
            foreach($view->getData() as $key => $value)
            {
                if(is_object($value))
                {
                    if(method_exists($value, 'toArray')){
                        $data[$key] = $value->toArray();
                    }else{
                        $data[$key] = "Object (". get_class($value).")";
                    }
                }else{
                    $data[$key] = $value;
                }
            }
            $this->data[$name] = $data ;
        }
    }

}
