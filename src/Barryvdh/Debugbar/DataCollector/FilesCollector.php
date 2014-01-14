<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\View\View;

class FilesCollector extends DataCollector  implements Renderable
{

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $files=get_included_files();
        $count=count($files);

        $messages = array();
        foreach($files as &$file){
            $messages[] = array(
                'message' => $file,
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
        return 'files';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        $name=$this->getName();
        return array(
            "$name" => array(
                "icon" => "files-o",
                "widget" => "PhpDebugBar.Widgets.MessagesWidget",
                "map" => "$name.messages",
                "default" => "{}"
            ),
            "$name:badge" => array(
                "map" => "$name.count",
                "default" => "null"
            )
        );
    }
}
