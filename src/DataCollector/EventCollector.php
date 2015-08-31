<?php
namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Events\Dispatcher;
use Symfony\Component\HttpKernel\DataCollector\Util\ValueExporter;

class EventCollector extends TimeDataCollector
{
    /** @var Dispatcher */
    protected $events;

    /** @var ValueExporter  */
    protected $exporter;

    public function __construct($requestStartTime = null)
    {
        parent::__construct($requestStartTime);

        $this->exporter = new ValueExporter();
    }

    public function onWildcardEvent()
    {
        $name = $this->events->firing();
        $time = microtime(true);

        $params = $this->prepareParams(func_get_args());

        foreach($this->events->getListeners($name) as $i => $listener) {
            if (is_array($listener) && count($listener) > 1 && is_object($listener[0])) {
                list($class, $method) = $listener;

                // Skip this class itself
                if ($class instanceof static) {
                    continue;
                }

                // Format thet listener to readable format
                $listener = get_class($class) . '@' . $method;
                
            } elseif ($listener instanceof \Closure) {
                $reflector = new \ReflectionFunction($listener);

                if($reflector->getNamespaceName() == 'Barryvdh\Debugbar') {
                    continue;
                }

                $filename = ltrim(str_replace(base_path(), '', $reflector->getFileName()), '/');
                $listener = $reflector->getName() . ' ('.$filename . ':' . $reflector->getStartLine() . '-' . $reflector->getEndLine() . ')';
            } else {
                $listener = $this->formatVar($listener);
            }

            $params['listeners.'.$i] = $listener;
        }
        $this->addMeasure($name, $time, $time,  $params);
    }

    public function subscribe(Dispatcher $events)
    {
        $this->events = $events;
        $events->listen('*', array($this, 'onWildcardEvent'));
    }

    protected function prepareParams($params)
    {
        $data = array();
        foreach ($params as $key => $value) {
            $data[$key] = htmlentities($this->exporter->exportValue($value), ENT_QUOTES, 'UTF-8', false);
        }
        return $data;
    }

    public function collect()
    {
        $data = parent::collect();
        $data['nb_measures'] = count($data['measures']);
        return $data;
    }

    public function getName()
    {
        return 'event';
    }

    public function getWidgets()
    {
        return array(
            "events" => array(
                "icon" => "tasks",
                "widget" => "PhpDebugBar.Widgets.TimelineWidget",
                "map" => "event",
                "default" => "{}"
            ),
            'events:badge' => array(
                'map' => 'event.nb_measures',
                'default' => 0
            )
        );
    }
}
