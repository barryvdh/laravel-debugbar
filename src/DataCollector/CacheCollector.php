<?php
namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Events\Dispatcher;

class CacheCollector extends TimeDataCollector
{
    /** @var bool */
    protected $collectValues;

    /** @var array */
    protected $classMap = [
        'Illuminate\Cache\Events\CacheHit' => 'hit',
        'Illuminate\Cache\Events\CacheMissed' => 'missed',
        'Illuminate\Cache\Events\KeyWritten' => 'write',
        'Illuminate\Cache\Events\KeyForgotten' => 'delete',
    ];

    public function __construct($requestStartTime = null, $collectValues)
    {
        parent::__construct();

        $this->collectValues = $collectValues;
    }

    public function onClassEvent($name, $event = null)
    {
        if(is_object($name)) {
            $event = $name;
        }

        if(is_array($event)) {
            $event = $event[0];
        }

        $class = get_class($event);
        if (isset($this->classMap[$class])) {
            $params = [];

            if(isset($event->minutes)) {
                $params['minutes'] = $event->minutes;
            }

            if(isset($event->value)) {
                if ($this->collectValues) {
                    $params['value'] = $this->getDataFormatter()->formatVar($event->value);
                } else {
                    $params['value'] = '(values collecting turned off)';
                }
            }

            if(!empty($event->tags)) {
                $params['tags'] = $event->tags;
            }

            $time = microtime(true);
            $this->addMeasure($this->classMap[$class] . ' ' . $event->key, $time, $time, $params);
        }
    }

    public function onStringEvent($event, $payload)
    {
        $params = [];

        if(is_array($payload)) {
            if (isset($payload[2])) {
                $params['minutes'] = $payload[2];
            }

            if (isset($payload[1])) {
                if ($this->collectValues) {
                    $params['value'] = $this->getDataFormatter()->formatVar($payload[1]);
                } else {
                    $params['value'] = '(values collecting turned off)';
                }
            }
        }

        $time = microtime(true);
        $this->addMeasure( str_replace('cache.', '', $event) . ' ' . (is_array($payload) ? $payload[0] : $payload),
            $time, $time, $params);
    }

    public function subscribe(Dispatcher $events)
    {
        if (class_exists('Illuminate\Cache\Events\CacheHit')) {
            $events->listen('Illuminate\Cache\Events\*', [$this, 'onClassEvent']);
        } else {
            $events->listen('cache.*', [$this, 'onStringEvent']);
        }
    }

    public function collect()
    {
        $data = parent::collect();
        $data['nb_measures'] = count($data['measures']);

        return $data;
    }

    public function getName()
    {
        return 'cache';
    }

    public function getWidgets()
    {
        return [
          'cache' => [
            'icon' => 'clipboard',
            'widget' => 'PhpDebugBar.Widgets.TimelineWidget',
            'map' => 'cache',
            'default' => '{}',
          ],
          'cache:badge' => [
            'map' => 'cache.nb_measures',
            'default' => 'null',
          ],
        ];
    }
}
