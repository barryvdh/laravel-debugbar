<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;

class RoutesCollector extends RouteCollector
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
        $routes = collect($this->router->getRoutes())
            ->map(function ($route) {
                return $this->getRouteInformation($route);
            })
            ->filter()
            ->keyBy(function ($item, $key) {
                return $item['as'] ?? $key;
            })
            ->map(function ($route) {
                return $this->formatVar($route);
            })
            ->all();

        return $routes;
    }


    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'routes';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        $widgets = [
            "routes" => [
                "icon" => "share-square-o",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "routes",
                "default" => "{}"
            ]
        ];

        return $widgets;
    }


}
