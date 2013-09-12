<?php namespace Barryvdh\Debugbar;

use Exception;

/**
 * Facade for Debugbar
 *
 * @method static void emergency($message)
 * @method static void alert($message)
 * @method static void critical($message)
 * @method static void error($message)
 * @method static void warning($message)
 * @method static void notice($message)
 * @method static void info($message)
 * @method static void debug($message)
 * @method static void log($message)
 *
 */
class Facade extends \Illuminate\Support\Facades\Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'debugbar'; }

    /**
     * Resolve a collector
     *
     * @param $name
     * @return mixed
     */
    public static function make($name){

        $instance = static::resolveFacadeInstance(static::getFacadeAccessor());
        return isset($instance[$name]) ? $instance[$name] : null;
    }

    /**
     * Starts a measure
     *
     * @param string $name Internal name, used to stop the measure
     * @param string $label Public name
     */
    public static function startMeasure($name, $label=null){
        /** @var \DebugBar\DataCollector\TimeDataCollector $time */
        $time = static::make('time');
        if($time){
            $time->startMeasure($name, $label);
        }

    }

    /**
     * Stops a measure
     *
     * @param string $name
     */
    public static function stopMeasure($name)
    {
        /** @var \DebugBar\DataCollector\TimeDataCollector $time */
        $time = static::make('time');
        if($time){
            $time->stopMeasure($name);
        }
    }

    /**
     * Utility function to measure the execution of a Closure
     *
     * @param string $label
     * @param \Closure|callable $closure
     */
    public static function measure($label, \Closure $closure)
    {
        /** @var \DebugBar\DataCollector\TimeDataCollector $time */
        $time = static::make('time');
        if($time){
            $time->measure($label, $closure);
        }
    }

    /**
     * Adds an exception to be profiled in the debug bar
     *
     * @param Exception $e
     */
    public static function addException(Exception $e)
    {
        /** @var \DebugBar\DataCollector\ExceptionsCollector $time */
        $exceptions = static::make('exceptions');
        if($exceptions){
            $exceptions->addException($e);
        }
    }

    /**
     * Adds a message
     *
     * A message can be anything from an object to a string
     *
     * @param mixed $message
     * @param string $label
     */
    public static function addMessage($message, $label = 'info')
    {
        /** @var \DebugBar\DataCollector\MessagesCollector $message */
        $message = static::make('messages');
        if($message){
            $message->addMessage($message, $label);
        }
    }
    /**
     * Override static calls for adding messages
     *
     * @param string $method
     * @param array $args
     * @return mixed|void
     */
    public static function __callStatic($method, $args)
    {
        $messageLevels = array('emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug', 'log');
        if(in_array($method, $messageLevels)){
            /** @var \DebugBar\DataCollector\MessagesCollector $message */
            $message = static::make('messages');
            if($message){
                $message->addMessage($args[0], $method);
            }
        }else{
            parent::__callStatic($method, $args);
        }
    }


}