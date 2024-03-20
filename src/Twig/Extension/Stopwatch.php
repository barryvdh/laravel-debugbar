<?php

namespace Barryvdh\Debugbar\Twig\Extension;

use DebugBar\Bridge\Twig\MeasureTwigExtension;
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
        $timeCollector = null;
        if ($app->bound('debugbar')) {
            $this->debugbar = $app['debugbar'];
            
            if ($app['debugbar']->hasCollector('time')) {
                $timeCollector = $app['debugbar']['time'];
            }
        }
        
        parent::__construct($timeCollector, 'stopwatch');
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return static::class;
    }

    public function getDebugbar()
    {
        return $this->debugbar;
    }
}
