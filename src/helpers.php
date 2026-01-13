<?php

declare(strict_types=1);

if (!function_exists('debugbar')) {
    /**
     * Get the Debugbar instance
     *
     */
    function debugbar(): \Fruitcake\LaravelDebugbar\LaravelDebugbar
    {
        return app(\Fruitcake\LaravelDebugbar\LaravelDebugbar::class);
    }
}

if (!function_exists('debug')) {
    /**
     * Adds one or more messages to the MessagesCollector
     *
     */
    function debug(mixed ...$value): void
    {
        $debugbar = debugbar();
        foreach ($value as $message) {
            $debugbar->addMessage($message, 'debug');
        }
    }
}
