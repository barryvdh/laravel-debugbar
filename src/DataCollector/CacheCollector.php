<?php
namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Cache\Events\CacheEvent;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Cache\Events\KeyForgotten;
use Illuminate\Cache\Events\KeyWritten;
use Illuminate\Events\Dispatcher;

class CacheCollector extends TimeDataCollector
{
    /** @var bool */
    protected $collectValues;

    /** @var array */
    protected $classMap = [
        CacheHit::class => 'hit',
        CacheMissed::class => 'missed',
        KeyWritten::class => 'written',
        KeyForgotten::class => 'forgotten',
    ];

    public function __construct($requestStartTime = null, $collectValues)
    {
        parent::__construct();

        $this->collectValues = $collectValues;
    }

    public function onCacheEvent(CacheEvent $event)
    {
        $class = get_class($event);
        $params = get_object_vars($event);

        if(isset($params['value'])) {
            if ($this->collectValues) {
                $params['value'] = $this->getDataFormatter()->formatVar($event->value);
            } else {
                unset($params['value']);
            }
        }

        $time = microtime(true);
        $this->addMeasure($this->classMap[$class] . "\t" . $event->key, $time, $time, $params);
    }


    public function subscribe(Dispatcher $dispatcher)
    {
        foreach ($this->classMap as $eventClass => $type) {
            $dispatcher->listen($eventClass, [$this, 'onCacheEvent']);
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
