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
     * A list of known editor strings.
     *
     * @var array
     */
    protected $editors = [
        'sublime' => 'subl://open?url=file://%file',
        'textmate' => 'txmt://open?url=file://%file',
        'emacs' => 'emacs://open?url=file://%file',
        'macvim' => 'mvim://open/?url=file://%file',
        'phpstorm' => 'phpstorm://open?file=%file',
        'idea' => 'idea://open?file=%file',
        'vscode' => 'vscode://file/%file',
        'vscode-insiders' => 'vscode-insiders://file/%file',
        'vscode-remote' => 'vscode://vscode-remote/%file',
        'vscode-insiders-remote' => 'vscode-insiders://vscode-remote/%file',
        'vscodium' => 'vscodium://file/%file',
        'nova' => 'nova://core/open/file?filename=%file',
        'xdebug' => 'xdebug://%file',
        'atom' => 'atom://core/open/file?filename=%file',
        'espresso' => 'x-espresso://open?filepath=%file',
        'netbeans' => 'netbeans://open/?f=%file',
    ];

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
     * Get the editor href for a given file and line, if available.
     *
     * @param string $filePath
     * @param int    $line
     *
     * @return null|string
     * @throws InvalidArgumentException If editor resolver does not return a string
     *
     */
    protected function getEditorLink($filePath)
    {
        if (empty(config('debugbar.editor'))) {
            return null;
        }

        if (empty($this->editors[ config('debugbar.editor') ])) {
            throw new InvalidArgumentException(
                'Unknown editor identifier: ' . config('debugbar.editor') . '. Known editors:' .
                implode(', ', array_keys($this->editors))
            );
        }

        $filePath = $this->replaceSitesPath($filePath);

        $url = str_replace(['%file'], [$filePath], $this->editors[ config('debugbar.editor') ]);

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

        if (!is_object($path)) {
            if ($path) {
                $editorLink = $this->getEditorLink($path);
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
            'editorLink' => $editorLink,
        ];

        if ($this->getXdebugLink($path)) {
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
