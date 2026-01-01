<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\TemplateCollector;
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

        if (is_object($path)) {
            $type = get_class($view);
            $path = null;
        }

        if ($path) {
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

        if ($this->timeCollector !== null) {
            $time = microtime(true);
            $this->timeCollector->addMeasure('View: ' . $name, $time, $time, [], 'views', 'View');
        }
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
