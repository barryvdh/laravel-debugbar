<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Livewire\Component;

/**
 * Collector for Models.
 */
class LivewireCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    public $data = [];

    public function __construct(Request $request)
    {
        // Listen to Livewire views
        Livewire::listen('view:render', function (View $view) use ($request) {
            /** @var \Livewire\Component $component */
            $component = $view->getData()['_instance'];

            // Create an unique name for each compoent
            $key = $component->getName() . ' #' . $component->id;

            $data = [
                'data' => $component->getPublicPropertiesDefinedBySubClass(),
            ];

            if ($request->request->get('id') == $component->id) {
                $data['oldData'] = $request->request->get('data');
                $data['actionQueue'] = $request->request->get('actionQueue');
            }

            $data['name'] = $component->getName();
            $data['view'] = $view->name();
            $data['component'] = get_class($component);
            $data['id'] = $component->id;

            $this->data[$key] = $this->formatVar($data);
        });

        Livewire::listen('render', function (Component $component) use ($request) {
            // Create an unique name for each compoent
            $key = $component->getName() . ' #' . $component->getId();

            $data = [
                'data' => $component->all(),
            ];

            if ($request->request->get('id') == $component->getId()) {
                $data['oldData'] = $request->request->get('data');
                $data['actionQueue'] = $request->request->get('actionQueue');
            }

            $data['name'] = $component->getName();
            $data['component'] = get_class($component);
            $data['id'] = $component->getId();

            $this->data[$key] = $this->formatVar($data);
        });
    }

    public function collect()
    {
        return ['data' => $this->data, 'count' => count($this->data)];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'livewire';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return [
            "livewire" => [
                "icon" => "bolt",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "livewire.data",
                "default" => "{}"
            ],
            'livewire:badge' => [
                'map' => 'livewire.count',
                'default' => 0
            ]
        ];
    }
}
