<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\View\View;
use Illuminate\Support\Facades\Config;
class ConfigCollector extends DataCollector  implements Renderable
{

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $views = array_dot(Config::getItems());
        $count=count($views);

        $messages = array();
        foreach($views as $key=>$value){
            $messages[] = array(
                'message' => $key.' => '.$this->formatVar($value),
                'is_string' => true,
            );
        }
        return array(
             'messages' => $messages,
             'count'=>$count
             );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'config';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        $name=$this->getName();
        return array(
            "$name" => array(
                "icon" => "gear",
                "widget" => "PhpDebugBar.Widgets.MessagesWidget",
                "map" => "$name.messages",
                "default" => "{}"
            )
        );
    }
}
