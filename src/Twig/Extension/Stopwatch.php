<?php

namespace Barryvdh\Debugbar\Twig\Extension;

use DebugBar\Bridge\Twig\MeasureTwigExtension;
use DebugBar\Bridge\Twig\MeasureTwigTokenParser;
use Illuminate\Foundation\Application;

/**
 * Access debugbar time measures in your Twig templates.
 * Based on Symfony\Bridge\Twig\Extension\StopwatchExtension
 */
class Stopwatch extends MeasureTwigExtension
{
    /**
     * @var \Barryvdh\Debugbar\LaravelDebugbar
     */
    protected $debugbar;

    /**
     * Create a new time measure extension.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        if ($app->bound('debugbar')) {
            $this->debugbar = $app['debugbar'];
        }

        parent::__construct(null, 'stopwatch');
    }


    public function getDebugbar()
    {
        return $this->debugbar;
    }

    public function getTokenParsers()
    {
        return [
            /*
             * {% measure foo %}
             * Some stuff which will be recorded on the timeline
             * {% endmeasure %}
             */
            new MeasureTwigTokenParser(!is_null($this->debugbar), $this->tagName, $this->getName()),
        ];
    }

    public function startMeasure(...$arg)
    {
        if (!$this->debugbar || !$this->debugbar->hasCollector('time')) {
            return;
        }

        $this->debugbar->getCollector('time')->startMeasure(...$arg);
    }

    public function stopMeasure(...$arg)
    {
        if (!$this->debugbar || !$this->debugbar->hasCollector('time')) {
            return;
        }

        $this->debugbar->getCollector('time')->stopMeasure(...$arg);
    }
}
