<?php

namespace Barryvdh\Debugbar\DataCollector;

use Barryvdh\Debugbar\DataFormatter\SimpleFormatter;
use DebugBar\DataCollector\AssetProvider;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ViewCollector extends DataCollector implements Renderable, AssetProvider
{
    protected $name;
    protected $templates = [];
    protected $collect_data;
    protected $exclude_paths;
    protected $group;
    protected $timeCollector;

    /**
     * Create a ViewCollector
     *
     * @param bool|string $collectData Collects view data when true
     * @param string[] $excludePaths Paths to exclude from collection
     * @param int|bool $group Group the same templates together
     * @param TimeDataCollector|null TimeCollector
     * */
    public function __construct($collectData = true, $excludePaths = [], $group = true, ?TimeDataCollector $timeCollector = null)
    {
        $this->setDataFormatter(new SimpleFormatter());
        $this->collect_data = $collectData;
        $this->templates = [];
        $this->exclude_paths = $excludePaths;
        $this->group = $group;
        $this->timeCollector = $timeCollector;
    }

    public function getName()
    {
        return 'views';
    }

    public function getWidgets()
    {
        return [
            'views' => [
                'icon' => 'leaf',
                'widget' => 'PhpDebugBar.Widgets.TemplatesWidget',
                'map' => 'views',
                'default' => '[]'
            ],
            'views:badge' => [
                'map' => 'views.nb_templates',
                'default' => 0
            ]
        ];
    }

    /**
     * @return array
     */
    public function getAssets()
    {
        return [
            'css' => 'widgets/templates/widget.css',
            'js' => 'widgets/templates/widget.js',
        ];
    }

    /**
     * Add a View instance to the Collector
     *
     * @param \Illuminate\View\View $view
     */
    public function addView(View $view)
    {
        $name = $view->getName();
        $type = null;
        $data = $view->getData();
        $path = $view->getPath();

        if (class_exists('\Inertia\Inertia')) {
            list($name, $type, $data, $path) = $this->getInertiaView($name, $data, $path);
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

    private function getInertiaView(string $name, array $data, ?string $path)
    {
        if (isset($data['page']) && is_array($data['page'])) {
            $data = $data['page'];
        }

        if (isset($data['props'], $data['component'])) {
            $name = $data['component'];
            $data = $data['props'];

            if ($files = glob(resource_path(config('debugbar.options.views.inertia_pages') .'/'. $name . '.*'))) {
                $path = $files[0];
                $type = pathinfo($path, PATHINFO_EXTENSION);

                if (in_array($type, ['js', 'jsx'])) {
                    $type = 'react';
                }
            }
        }

        return [$name, $type ?? '', $data, $path];
    }

    public function addInertiaAjaxView(array $data)
    {
        list($name, $type, $data, $path) = $this->getInertiaView('', $data, '');

        if (! $name) {
            return;
        }

        $this->addTemplate($name, $data, $type, $path);
    }

    private function addTemplate(string $name, array $data, ?string $type, ?string $path)
    {
        // Prevent duplicates
        $hash = $type . $path . $name . ($this->collect_data ? implode(array_keys($data)) : '');

        if ($this->collect_data === 'keys') {
            $params = array_keys($data);
        } elseif ($this->collect_data) {
            $params = array_map(
                fn ($value) => $this->getDataFormatter()->formatVar($value),
                $data
            );
        } else {
            $params = [];
        }

        $template = [
            'name' => $name,
            'param_count' => $this->collect_data ? count($params) : null,
            'params' => $params,
            'start' => microtime(true),
            'type' => $type,
            'hash' => $hash,
        ];

        if ($path && $this->getXdebugLinkTemplate()) {
            $template['xdebug_link'] = $this->getXdebugLink($path);
        }

        $this->templates[] = $template;
    }

    public function collect()
    {
        if ($this->group === true || count($this->templates) > $this->group) {
            $templates = [];
            foreach ($this->templates as $template) {
                $hash = $template['hash'];
                if (!isset($templates[$hash])) {
                    $template['render_count'] = 0;
                    $template['name_original'] = $template['name'];
                    $templates[$hash] = $template;
                }

                $templates[$hash]['render_count']++;
                $templates[$hash]['name'] = $templates[$hash]['render_count'] . 'x ' . $templates[$hash]['name_original'];
            }
            $templates = array_values($templates);
        } else {
            $templates = $this->templates;
        }

        return [
            'count' => count($this->templates),
            'nb_templates' => count($this->templates),
            'templates' => $templates,
        ];
    }
}
