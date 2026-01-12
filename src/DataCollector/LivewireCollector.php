<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\DataCollector;

use DebugBar\DataCollector\TemplateCollector;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Livewire\Livewire;
use Livewire\Component;

/**
 * Collector for Models.
 */
class LivewireCollector extends TemplateCollector
{
    public function __construct(Request $request)
    {
        parent::__construct(true, [], false);

        // Listen to Livewire views
        Livewire::listen('view:render', function (View $view) use ($request) {
            /** @var \Livewire\Component $component */
            $component = $view->getData()['_instance'];

            $key = get_class($component) . ' ' . $component->getName() . ' #' . $component->id;

            $data = $component->getPublicPropertiesDefinedBySubClass();

            if ($request->request->get('id') == $component->id) {
                $data['#oldData'] = $request->request->get('data');
                $data['#actionQueue'] = $request->request->get('actionQueue');
            }

            $data['#name'] = $component->getName();
            $data['#view'] = $view->name();
            $data['#component'] = get_class($component);
            $data['#id'] = $component->id;

            $path = (new \ReflectionClass($component))->getFileName();

            $this->addTemplate($key, $data, 'livewire', $path);
        });

        Livewire::listen('render', function (Component $component) use ($request) {
            // Create an unique name for each component

            if ((new \ReflectionClass($component))->isAnonymous()) {
                $key = $component->getName() . ' #' . $component->getId();
            } else {
                $key = get_class($component) . ' ' . $component->getName() . ' #' . $component->getId();
            }

            $data = $component->all();

            if ($request->request->get('id') == $component->getId()) {
                $data['#oldData'] = $request->request->get('data');
                $data['#actionQueue'] = $request->request->get('actionQueue');
            }

            $data['#name'] = $component->getName();
            $data['#component'] = get_class($component);
            $data['#id'] = $component->getId();

            $path = (new \ReflectionClass($component))->getFileName();
            ;
            $this->addTemplate($key, $data, 'livewire', $path);
        });
    }

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

    public function getWidgets(): array
    {
        $widgets = parent::getWidgets();
        $widgets[$this->getName()]['icon'] = 'brand-livewire';
        return $widgets;
    }
}
