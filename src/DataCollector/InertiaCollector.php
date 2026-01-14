<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\DataCollector;

use DebugBar\DataCollector\TemplateCollector;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

/**
 * Collector for Ineratia.
 */
class InertiaCollector extends TemplateCollector
{
    public function addView(\Illuminate\View\View $view): void
    {
        $name = $view->getName();
        $data = $view->getData();
        $path = $view->getPath();

        [$name, $type, $data, $path] = $this->getInertiaView($name, $data, $path);

        $this->addTemplate($name, $data, $type, $path);
    }

    public function addFromResponse(Response $response)
    {
        $content = $response->getContent();

        if (is_string($content)) {
            $content = json_decode($content, true);
        }

        if (is_array($content)) {
            [$name, $type, $data, $path] = $this->getInertiaView('', $content, '');

            if ($name) {
                $this->addTemplate($name, $data, $type, $path);
            }
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

            if ($files = glob(resource_path(config('debugbar.options.inertia.pages') . '/' . $name . '.*'))) {
                $path = $files[0];
                $type = pathinfo($path, PATHINFO_EXTENSION);

                if (in_array($type, ['js', 'jsx'], true)) {
                    $type = 'react';
                }
            }
        }

        return [$name, $type ?? '', $data, $path];
    }

    public function collect(): array
    {
        $data = parent::collect();

        $data['sentence'] = 'Inertia page' . ($data['nb_templates'] !== 1 ? 's' : '');

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'inertia';
    }

    public function getWidgets(): array
    {
        $widgets = parent::getWidgets();
        $widgets[$this->getName()]['icon'] = 'brand-inertia';
        return $widgets;
    }
}
