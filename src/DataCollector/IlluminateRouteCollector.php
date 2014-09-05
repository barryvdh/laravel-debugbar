<?php

namespace Barryvdh\Debugbar\DataCollector;

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
class IlluminateRouteCollector extends DataCollector implements Renderable
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

        $route = \Route::current();
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
            return array();
        }
        $uri = head($route->methods()) . ' ' . $route->uri();

        return array(
            'host' => $route->domain() ?: '-',
            'uri' => $uri ?: '-',
            'name' => $route->getName() ?: '-',
            'action' => $route->getActionName() ?: '-',
            'before' => $this->getBeforeFilters($route) ?: '-',
            'after' => $this->getAfterFilters($route) ?: '-'
        );
    }

    /**
     * Get before filters
     *
     * @param  \Illuminate\Routing\Route $route
     * @return string
     */
    protected function getBeforeFilters($route)
    {
        $before = array_keys($route->beforeFilters());

        $before = array_unique(array_merge($before, $this->getPatternFilters($route)));

        return implode(', ', $before);
    }

    /*
     * The following is copied/modified from the RoutesCommand from Laravel, by Taylor Otwell
     * https://github.com/laravel/framework/blob/4.1/src/Illuminate/Foundation/Console/RoutesCommand.php
     *
     */

    /**
     * Get all of the pattern filters matching the route.
     *
     * @param  \Illuminate\Routing\Route $route
     * @return array
     */
    protected function getPatternFilters($route)
    {
        $patterns = array();

        foreach ($route->methods() as $method) {
            // For each method supported by the route we will need to gather up the patterned
            // filters for that method. We will then merge these in with the other filters
            // we have already gathered up then return them back out to these consumers.
            $inner = $this->getMethodPatterns($route->uri(), $method);

            $patterns = array_merge($patterns, array_keys($inner));
        }

        return $patterns;
    }

    /**
     * Get the pattern filters for a given URI and method.
     *
     * @param  string $uri
     * @param  string $method
     * @return array
     */
    protected function getMethodPatterns($uri, $method)
    {
        return $this->router->findPatternFilters(Request::create($uri, $method));
    }

    /**
     * Get after filters
     *
     * @param  Route $route
     * @return string
     */
    protected function getAfterFilters($route)
    {
        return implode(', ', array_keys($route->afterFilters()));
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
        $widgets = array(
            "route" => array(
                "icon" => "share",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "route",
                "default" => "{}"
            )
        );
        if (Config::get('laravel-debugbar::config.options.route.label', true)) {
            $widgets['currentroute'] = array(
                "icon" => "share",
                "tooltip" => "Route",
                "map" => "route.uri",
                "default" => ""
            );
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
