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

    /**
     * Create a ViewCollector
     *
     * @param bool $collectData Collects view data when true
     * @param string[] $excludePaths Paths to exclude from collection
     */
    public function __construct($collectData = true, $excludePaths = [])
    {
        $this->setDataFormatter(new SimpleFormatter());
        $this->collect_data = $collectData;
        $this->templates = [];
        $this->exclude_paths = $excludePaths;
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
        $type = '';

        if ($path && is_string($path)) {
            $path = $this->normalizeFilePath($path);

            if (substr($path, -10) == '.blade.php') {
                $type = 'blade';
            } else {
                $type = pathinfo($path, PATHINFO_EXTENSION);
            }
        } elseif (is_object($path)) {
            $type = get_class($view);
            $path = '';
        }

        foreach ($this->exclude_paths as $excludePath) {
            if (str_starts_with($path, $excludePath)) {
                return;
            }
        }

        if (!$this->collect_data) {
            $params = array_keys($view->getData());
        } else {
            $data = [];
            foreach ($view->getData() as $key => $value) {
                $data[$key] = $this->getDataFormatter()->formatVar($value);
            }
            $params = $data;
        }

        $template = [
            'name' => $path ? sprintf('%s (%s)', $name, $path) : $name,
            'param_count' => count($params),
            'params' => $params,
            'type' => $type,            
        ];

        if ($this->getXdebugLinkTemplate()) {
            $template['xdebug_link'] = $this->getXdebugLink(realpath($view->getPath()));
        }

        $this->templates[] = $template;
    }

    public function collect()
    {
        $templates = $this->templates;

        return [
            'nb_templates' => count($templates),
            'templates' => $templates,
        ];
    }
}
