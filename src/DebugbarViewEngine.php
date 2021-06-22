<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar;

use Illuminate\Contracts\View\Engine;

class DebugbarViewEngine implements Engine
{
    /**
     * @var Engine
     */
    protected $engine;

    /**
     * @var LaravelDebugbar
     */
    protected $laravelDebugbar;

    /**
     * @param  Engine  $engine
     * @param  LaravelDebugbar  $laravelDebugbar
     */
    public function __construct(Engine $engine, LaravelDebugbar $laravelDebugbar)
    {
        $this->engine = $engine;
        $this->laravelDebugbar = $laravelDebugbar;
    }

    /**
     * @param  string  $path
     * @param  array  $data
     * @return string
     */
    public function get($path, array $data = [])
    {
        $shortPath = ltrim(str_replace(base_path(), '', realpath($path)), '/');

        return $this->laravelDebugbar->measure($shortPath, function () use ($path, $data) {
            return $this->engine->get($path, $data);
        });
    }

    /**
     * NOTE: This is done to support other Engine swap (example: Livewire).
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->engine->$name(...$arguments);
    }
}
