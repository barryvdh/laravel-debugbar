<?php namespace Barryvdh\Debugbar;

class Facade extends \Illuminate\Support\Facades\Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'debugbar'; }


    protected static function make($key){

        $instance = static::resolveFacadeInstance(static::getFacadeAccessor());
        return $instance[$key];
    }

    public static function __callStatic($method, $args)
    {
        $messageLevels = array('emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug', 'log');
        if(in_array($method, $messageLevels)){
            $message = static::make('messages');
            $message->addMessage($args[0], $method);
        }else{
            parent::__callStatic($method, $args);
        }
    }


}