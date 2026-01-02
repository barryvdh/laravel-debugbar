<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\Twig\Extension;

use DebugBar\Bridge\Twig\DebugTwigExtension;
use Illuminate\Foundation\Application;
use Twig\Environment;

/**
 * Access debugbar debug in your Twig templates.
 */
class Debug extends DebugTwigExtension
{
    protected $app;

    /**
     * Create a new debug extension.
     *
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        parent::__construct(null);
    }

    public function debug(Environment $env, $context)
    {
        if ($this->app->bound('debugbar') && $this->app['debugbar']->hasCollector('messages')) {
            $this->messagesCollector = $this->app['debugbar']['messages'];
        }

        return parent::debug($env, $context);
    }
}
