<?php

namespace Barryvdh\Debugbar\DataCollector;

use Barryvdh\Debugbar\DataFormatter\SimpleFormatter;
use DebugBar\Bridge\Twig\TwigCollector;
use Illuminate\View\View;
use InvalidArgumentException;

class ViewCollector extends TwigCollector
{
    protected $name;
    protected $templates = [];
    protected $collect_data;
    protected $exclude_paths;

    /**
     * A list of known editor strings.
     *
     * @var array
     */
    protected $editors = [
        'sublime' => 'subl://open?url=file://%file&line=%line',
        'textmate' => 'txmt://open?url=file://%file&line=%line',
        'emacs' => 'emacs://open?url=file://%file&line=%line',
        'macvim' => 'mvim://open/?url=file://%file&line=%line',
        'phpstorm' => 'phpstorm://open?file=%file&line=%line',
        'idea' => 'idea://open?file=%file&line=%line',
        'vscode' => 'vscode://file/%file:%line',
        'vscode-insiders' => 'vscode-insiders://file/%file:%line',
        'vscode-remote' => 'vscode://vscode-remote/%file:%line',
        'vscode-insiders-remote' => 'vscode-insiders://vscode-remote/%file:%line',
        'vscodium' => 'vscodium://file/%file:%line',
        'nova' => 'nova://core/open/file?filename=%file&line=%line',
        'xdebug' => 'xdebug://%file@%line',
        'atom' => 'atom://core/open/file?filename=%file&line=%line',
        'espresso' => 'x-espresso://open?filepath=%file&lines=%line',
        'netbeans' => 'netbeans://open/?f=%file:%line',
    ];

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
     * Get the editor href for a given file and line, if available.
     *
     * @param string $filePath
     * @param int    $line
     *
     * @throws InvalidArgumentException If editor resolver does not return a string
     *
     * @return null|string
     */
    protected function getEditorHref($filePath, $line)
    {
        if (empty(config('debugbar.editor'))) {
            return null;
        }

        if (empty($this->editors[config('debugbar.editor')])) {
            throw new InvalidArgumentException(
                'Unknown editor identifier: ' . config('debugbar.editor') . '. Known editors:' .
                implode(', ', array_keys($this->editors))
            );
        }

        $filePath = $this->replaceSitesPath($filePath);

        $url = str_replace(['%file', '%line'], [$filePath, $line], $this->editors[config('debugbar.editor')]);

        return $url;
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
            $path = realpath($path);
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
            if (strpos($shortPath, $excludePath) !== false) {
                return;
            }
        }

        $params = !$this->collect_data ? array_keys($data) : array_map(
            fn ($value) => $this->getDataFormatter()->formatVar($value),
            $data
        );

        $template = [
            'name' => $shortPath ? sprintf('%s (%s)', $name, $shortPath) : $name,
            'param_count' => count($params),
            'params' => $params,
            'type' => $type,
            'editorLink' => $this->getEditorHref($path, 1),
        ];

        if ($this->getXdebugLinkTemplate()) {
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

    /**
     * Replace remote path
     *
     * @param string $filePath
     *
     * @return string
     */
    protected function replaceSitesPath($filePath)
    {
        return str_replace(config('debugbar.remote_sites_path'), config('debugbar.local_sites_path'), $filePath);
    }
}
