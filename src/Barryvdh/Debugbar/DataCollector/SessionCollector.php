<?php
namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\MessagesCollector;

class SessionCollector extends MessagesCollector
{
    
    public function __construct($name = 'sessions')
    {
        $this->name = $name;
        $sessions=\Session::all();
         foreach($sessions as $key=>&$session){
            $this->addMessage( $key.' => '.print_r($session,true));
         }
        
    }
    
}