<?php

namespace Barryvdh\Debugbar\DataCollector;

use Closure;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

/**
 * Based on Illuminate\Foundation\Console\RoutesCommand for Taylor Otwell
 * https://github.com/laravel/framework/blob/master/src/Illuminate/Foundation/Console/RoutesCommand.php
 *
 */
class RouteCollector extends DataCollector implements Renderable
{
    /**
     * The router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

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
        'vscodium' => 'vscodium://file/%file:%line',
        'nova' => 'nova://core/open/file?filename=%file&line=%line',
        'xdebug' => 'xdebug://%file@%line',
        'atom' => 'atom://core/open/file?filename=%file&line=%line',
        'espresso' => 'x-espresso://open?filepath=%file&lines=%line',
        'netbeans' => 'netbeans://open/?f=%file:%line',
    ];

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $route = $this->router->current();
        return $this->getRouteInformation($route);
    }

    /**
     * Get the route information for a given route.
     *
     * @param  \Illuminate\Routing\Route $route
     * @return array
     */
    protected function getRouteInformation($route)
    {
        if (!is_a($route, 'Illuminate\Routing\Route')) {
            return [];
        }
        $uri = head($route->methods()) . ' ' . $route->uri();
        $action = $route->getAction();

        $result = [
           'uri' => $uri ?: '-',
        ];

        $result = array_merge($result, $action);


        if (
            isset($action['controller'])
            && is_string($action['controller'])
            && strpos($action['controller'], '@') !== false
        ) {
            list($controller, $method) = explode('@', $action['controller']);
            if (class_exists($controller) && method_exists($controller, $method)) {
                $reflector = new \ReflectionMethod($controller, $method);
            }
            unset($result['uses']);
        } elseif (isset($action['uses']) && $action['uses'] instanceof \Closure) {
            $reflector = new \ReflectionFunction($action['uses']);
            $result['uses'] = $this->formatVar($result['uses']);
        }

        if (isset($reflector)) {
            $filename = ltrim(str_replace(base_path(), '', $reflector->getFileName()), '/');

            if ($href = $this->getEditorHref($reflector->getFileName(), $reflector->getStartLine())) {
                $result['file'] = sprintf('<a href="%s">%s:%s-%s</a>', $href, $filename, $reflector->getStartLine(), $reflector->getEndLine());
            } else {
                $result['file'] = sprintf('%s:%s-%s', $filename, $reflector->getStartLine(), $reflector->getEndLine());
            }
        }

        if ($middleware = $this->getMiddleware($route)) {
            $result['middleware'] = $middleware;
        }



        return $result;
    }

    /**
     * Get middleware
     *
     * @param  \Illuminate\Routing\Route $route
     * @return string
     */
    protected function getMiddleware($route)
    {
        return implode(', ', array_map(function ($middleware) {
            return $middleware instanceof Closure ? 'Closure' : $middleware;
        }, $route->middleware()));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'route';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        $widgets = [
            "route" => [
                "icon" => "share",
                "widget" => "PhpDebugBar.Widgets.HtmlVariableListWidget",
                "map" => "route",
                "default" => "{}"
            ]
        ];
        if (Config::get('debugbar.options.route.label', true)) {
            $widgets['currentroute'] = [
                "icon" => "share",
                "tooltip" => "Route",
                "map" => "route.uri",
                "default" => ""
            ];
        }
        return $widgets;
    }

    /**
     * Display the route information on the console.
     *
     * @param  array $routes
     * @return void
     */
    protected function displayRoutes(array $routes)
    {
        $this->table->setHeaders($this->headers)->setRows($routes);

        $this->table->render($this->getOutput());
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
