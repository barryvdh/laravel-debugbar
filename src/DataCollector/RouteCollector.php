<?php

namespace Barryvdh\Debugbar\DataCollector;

use Closure;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;

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
            $filename = $this->normalizeFilePath($reflector->getFileName());

            if ($link = $this->getXdebugLink($reflector->getFileName(), $reflector->getStartLine())) {
                $result['file'] = sprintf(
                    '<a href="%s" onclick="%s">%s:%s-%s</a>',
                    $link['url'],
                    $link['ajax'] ? 'event.preventDefault();$.ajax(this.href);' : '',
                    $filename,
                    $reflector->getStartLine(),
                    $reflector->getEndLine()
                );
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
        }, $route->gatherMiddleware()));
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
}
