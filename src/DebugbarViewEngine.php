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
     * @var array
     */
    protected $exclude_paths;

    /**
     * @param  Engine  $engine
     * @param  LaravelDebugbar  $laravelDebugbar
     */
    public function __construct(Engine $engine, LaravelDebugbar $laravelDebugbar)
    {
        $this->engine = $engine;
        $this->laravelDebugbar = $laravelDebugbar;
        $this->exclude_paths = app('config')->get('debugbar.options.views.exclude_paths', []);
    }

    /**
     * @param  string  $path
     * @param  array  $data
     * @return string
     */
    public function get($path, array $data = [])
    {
        $basePath = base_path();
        $shortPath = @file_exists((string) $path) ? realpath($path) : $path;

        if (str_starts_with($shortPath, $basePath)) {
            $shortPath = ltrim(
                str_replace('\\', '/', substr($shortPath, strlen($basePath))),
                '/'
            );
        }

        foreach ($this->exclude_paths as $excludePath) {
            if (str_starts_with($shortPath, $excludePath)) {
                return $this->engine->get($path, $data);
            }
        }

        return $this->laravelDebugbar->measure($shortPath, function () use ($path, $data) {
            return $this->engine->get($path, $data);
        }, 'views');
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
