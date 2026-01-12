<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\DataCollector;

use DebugBar\DataCollector\TemplateCollector;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ViewCollector extends TemplateCollector
{
    public function getName(): string
    {
        return 'views';
    }

    /**
     * Add a View instance to the Collector
     */
    public function addView(View $view): void
    {
        $name = $view->getName();
        $type = null;
        $data = $view->getData();
        $path = $view->getPath();

        if (class_exists('\Inertia\Inertia')) {
            [$name, $type, $data, $path] = $this->getInertiaView($name, $data, $path);
        }

        // Skip View files from strings
        if (Str::startsWith($name, '__components::')) {
            if ($source = $this->getRenderSource($name, $path)) {
                [$name, $type, $data, $path] = $source;
            }
        }

        if (is_object($path)) {
            $type = get_class($view);
            $path = null;
        }

        if ($path && $type !== 'livewire') {
            if (!$type) {
                if (substr($path, -10) == '.blade.php') {
                    $type = 'blade';
                } else {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                }
            }

            $shortPath = $this->normalizeFilePath($path);
            foreach ($this->exclude_paths as $excludePath) {
                if (str_starts_with($shortPath, $excludePath)) {
                    return;
                }
            }
        }

        $this->addTemplate($name, $data, $type, $path);
    }

    private function getRenderSource(string $name, ?string $path): ?array
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 20);

        $component = null;
        $render = null;
        $view = null;
        foreach ($backtrace as $trace) {
            $function = $trace['function'] ?? null;
            $class = $trace['class'] ?? null;
            $file = $trace['file'] ?? null;
            $object = $trace['object'] ?? null;
            // Found an invokable class
            if (
                $function === '__invoke'
                && $class == 'Livewire\Component'
                && $object
                && !$component
            ) {
                /** @var \Livewire\Component $component */
                $component = $trace['object'];
                $name = get_class($component);
                $type = 'livewire';
                $path = (new \ReflectionClass($component))->getFileName();
                $component = [$name, $type, [], $path];
            }
            if (
                (
                    ($function === 'render' && $class == 'Illuminate\View\Compilers\BladeCompiler')
                    || ($function === '__callStatic' && $class == 'Illuminate\Support\Facades\Facade' && ($trace['args'][0] ?? null) == 'render')
                )
                && !str_contains($file, '/Illuminate/')
                && !$render
            ) {
                $render = [$name, 'render', [], $file];
            }

            if (!$view && $class == 'Illuminate\View\View' && $object instanceof View && !str_starts_with($object->getName(), '__components::')
            ) {
                $view  = [$object->getName(), null, $object->getData(), $object->getPath()];
            }
        }

        if ($component) {
            return $component;
        }

        if ($render) {
            return $render;
        }

        if ($view) {
            return $view;
        }

        return null;
    }

    private function getInertiaView(string $name, array $data, ?string $path): array
    {
        if (isset($data['page']) && is_array($data['page'])) {
            $data = $data['page'];
        }

        if (isset($data['props'], $data['component'])) {
            $name = $data['component'];
            $data = $data['props'];

            if ($files = glob(resource_path(config('debugbar.options.views.inertia_pages') . '/' . $name . '.*'))) {
                $path = $files[0];
                $type = pathinfo($path, PATHINFO_EXTENSION);

                if (in_array($type, ['js', 'jsx'])) {
                    $type = 'react';
                }
            }
        }

        return [$name, $type ?? '', $data, $path];
    }

    public function addInertiaAjaxView(array $data): void
    {
        [$name, $type, $data, $path] = $this->getInertiaView('', $data, '');

        if (! $name) {
            return;
        }

        $this->addTemplate($name, $data, $type, $path);
    }
}
