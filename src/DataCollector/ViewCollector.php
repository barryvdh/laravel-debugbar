<?php

namespace Barryvdh\Debugbar\DataCollector;

use Barryvdh\Debugbar\DataFormatter\SimpleFormatter;
use DebugBar\Bridge\Twig\TwigCollector;
use Illuminate\View\View;

class ViewCollector extends TwigCollector
{
    protected $name;
    protected $templates = [];
    protected $collect_data;
    protected $exclude_paths;
    protected $group;

    /**
     * Create a ViewCollector
     *
     * @param bool|string $collectData Collects view data when true
     * @param string[] $excludePaths Paths to exclude from collection
     * @param bool $group Group the same templates together
     * */
    public function __construct($collectData = true, $excludePaths = [], $group = true)
    {
        $this->setDataFormatter(new SimpleFormatter());
        $this->collect_data = $collectData;
        $this->templates = [];
        $this->exclude_paths = $excludePaths;
        $this->group = $group;
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
     * Add a View instance to the Collector
     *
     * @param \Illuminate\View\View $view
     */
    public function addView(View $view)
    {
        $name = $view->getName();
        $path = $view->getPath();
        $data = $view->getData();
        $shortPath = '';
        $type = '';


        // Prevent duplicates
        $hash = $type . $path . $name . $this->collect_data ? implode(array_keys($view->getData())) : '';
        if ($this->group && isset($this->templates[$hash])) {
            $this->templates[$hash]['render_count']++;
            return;
        }

        if (class_exists('\Inertia\Inertia') && isset($data['page'])) {
            $data = $data['page'];
            $name = $data['component'];

            if (!@file_exists($path = resource_path('js/Pages/' . $name . '.js'))) {
                if (!@file_exists($path = resource_path('js/Pages/' . $name . '.vue'))) {
                    if (!@file_exists($path = resource_path('js/Pages/' . $name . '.svelte'))) {
                        $path = $view->getPath();
                    }
                }
            } else {
                $type = 'react';
            }
        }

        if ($path && is_string($path)) {
            $path = $this->normalizeFilePath($path);
            $shortPath = ltrim(str_replace(base_path(), '', $path), '/');
        } elseif (is_object($path)) {
            $type = get_class($view);
            $path = '';
        }

        if ($path && !$type) {
            if (substr($path, -10) == '.blade.php') {
                $type = 'blade';
            } else {
                $type = pathinfo($path, PATHINFO_EXTENSION);
            }
        }

        foreach ($this->exclude_paths as $excludePath) {
            if (str_starts_with($path, $excludePath)) {
                return;
            }
        }

        if ($this->collect_data === 'keys') {
            $params = array_keys($view->getData());
        } elseif ($this->collect_data) {
            $params = array_map(
                fn ($value) => $this->getDataFormatter()->formatVar($value),
                $data
            );
        } else {
            $params = [];
        }


        $template = [
            'name' => $shortPath ? sprintf('%s (%s)', $name, $shortPath) : $name,
            'param_count' => count($params),
            'params' => $params,
            'start' => microtime(true),
            'type' => $type,
            'render_count' => 1,
        ];

        if ($this->getXdebugLinkTemplate()) {
            $template['xdebug_link'] = $this->getXdebugLink(realpath($view->getPath()));
        }

        $this->templates[$hash] = $template;
    }

    public function collect()
    {
        $templates = $this->templates;
        if ($this->group) {
            foreach ($templates as &$template) {
                $template['name'] = $template['render_count'] . 'x ' . $template['name'];
            }
        }

        return [
            'nb_templates' => count($templates),
            'templates' => array_values($templates),
        ];
    }
}
