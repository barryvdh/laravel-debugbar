<?php

namespace Barryvdh\Debugbar\DataCollector;

use Barryvdh\Debugbar\DataFormatter\SimpleFormatter;
use DebugBar\Bridge\Twig\TwigCollector;
use Illuminate\View\View;
use Symfony\Component\VarDumper\Cloner\VarCloner;

class ViewCollector extends TwigCollector
{
    protected $templates = [];
    protected $collect_data;

    /**
     * Create a ViewCollector
     *
     * @param bool $collectData Collects view data when tru
     */
    public function __construct($collectData = true)
    {
        $this->setDataFormatter(new SimpleFormatter());
        $this->collect_data = $collectData;
        $this->name = 'views';
        $this->templates = [];
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
        
        if (!is_object($path)) {
            if ($path) {
                $path = ltrim(str_replace(base_path(), '', realpath($path)), '/');
            }

            if (substr($path, -10) == '.blade.php') {
                $type = 'blade';
            } else {
                $type = pathinfo($path, PATHINFO_EXTENSION);
            }
        } else {
            $type = get_class($view);
            $path = '';
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

        if ( $this->getXdebugLink($path)) {
            $template['xdebug_link'] = $this->getXdebugLink($path);
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
