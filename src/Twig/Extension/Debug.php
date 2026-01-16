<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\Twig\Extension;

use DebugBar\Bridge\Twig\DebugTwigExtension;
use Twig\Environment;

/**
 * Access debugbar debug in your Twig templates.
 */
class Debug extends DebugTwigExtension
{
    public function debug(Environment $env, $context)
    {
        $app = app();
        if ($app->bound('debugbar') && $app['debugbar']->hasCollector('messages')) {
            $this->messagesCollector = $app['debugbar']['messages'];
        }

        parent::debug($env, $context);
    }
}
