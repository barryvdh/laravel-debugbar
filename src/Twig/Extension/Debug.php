<?php

namespace Barryvdh\Debugbar\Twig\Extension;

use DebugBar\Bridge\Twig\DebugTwigExtension;
use Illuminate\Foundation\Application;

/**
 * Access debugbar debug in your Twig templates.
 */
class Debug extends DebugTwigExtension
{
    /**
     * Create a new debug extension.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $messagesCollector = null;
        if ($app->bound('debugbar') && $app['debugbar']->hasCollector('messages')) {
            $messagesCollector = $app['debugbar']['messages'];
        }

        parent::__construct($messagesCollector);
    }
}
