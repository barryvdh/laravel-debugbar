<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\DataCollector;

use DebugBar\DataCollector\TemplateCollector;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Livewire\Component;

/**
 * Collector for Models.
 */
class LivewireCollector extends TemplateCollector
{
    public function addLivewire2View(View $view, ?Request $request = null): void
    {
        $component = $view->getData()['_instance'];
        $id = $component->id;
        $data = $component->getPublicPropertiesDefinedBySubClass();

        $this->addLivewireTemplate($component, $id, $data, $request);
    }

    public function addLivewireComponent(Component $component, ?Request $request = null): void
    {
        $id = $component->getId();
        $data = $component->all();

        $this->addLivewireTemplate($component, $id, $data, $request);
    }

    protected function addLivewireTemplate(Component $component, ?string $id, array $data, ?Request $request = null): void
    {
        if ((new \ReflectionClass($component))->isAnonymous()) {
            $key = Str::ascii($component->getName()) . ' #' . $id;
        } else {
            $key = get_class($component) . ' ' . $component->getName() . ' #' . $id;
        }

        if ($request && $request->request->get('id') === $id) {
            $data['#oldData'] = $request->request->get('data');
            $data['#actionQueue'] = $request->request->get('actionQueue');
        }

        $data['#name'] = $component->getName();
        $data['#component'] = get_class($component);
        $data['#id'] = $id;

        $path = (new \ReflectionClass($component))->getFileName();

        $this->addTemplate($key, $data, 'livewire', $path);
    }

    /**
     * @return array{nb_templates: int, templates: array<string, array{name: string, param_count: int, params: array<string, mixed>, type: string, xdebug_link?: string}>, sentence: string}
     */
    public function collect(): array
    {
        $data = parent::collect();

        $data['sentence'] = 'Livewire component' . ($data['nb_templates'] !== 1 ? 's' : '');

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'livewire';
    }

    /**
     * @return array<string, array{icon: string, widget: string, map: string, default: string}>
     */
    public function getWidgets(): array
    {
        $widgets = parent::getWidgets();
        $widgets[$this->getName()]['icon'] = 'brand-livewire';
        return $widgets;
    }
}
