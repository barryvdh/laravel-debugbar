<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\DataCollector;

use DebugBar\Bridge\Symfony\SymfonyRequestCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Illuminate\Support\Str;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;

class RequestCollector extends SymfonyRequestCollector implements DataCollectorInterface, Renderable
{
    protected ?string $currentRequestId = null;

    public function setCurrentRequestId(?string $requestId): void
    {
        $this->currentRequestId = $requestId;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(): array
    {
        $result = parent::collect();
        $htmlData = [];

        if ($this->request instanceof \Illuminate\Http\Request) {
            $htmlData += $this->getRouteInformation($this->request->route());
        }

        if (class_exists(Telescope::class) && class_exists(IncomingEntry::class)) {
            $entry = IncomingEntry::make([
                'requestId' => $this->currentRequestId,
            ])->type('debugbar');
            Telescope::$entriesQueue[] = $entry;
            $url = route('debugbar.telescope', [$entry->uuid]);
            $htmlData['telescope'] = '<a href="' . $url . '" target="_blank">View in Telescope</a>';
        }

        if ($this->request instanceof \Illuminate\Http\Request) {
            $result['tooltip'] += [
                'full_url' => Str::limit($this->request->fullUrl(), 100),
                'action_name' => $this->request->route()->getName(),
                'controller_action' => $this->request->route()->getActionName(),
            ];
        }

        unset($htmlData['as'], $htmlData['uses']);

        $result['data'] = $htmlData + $result['data'];

        return $result;
    }

    protected function getRouteInformation(mixed $route): array
    {
        if (!is_a($route, 'Illuminate\Routing\Route')) {
            return [];
        }
        $uri = head($route->methods()) . ' ' . $route->uri();
        $action = $route->getAction();

        $result = [
            'uri' => $uri,
        ];

        $result = array_merge($result, $action);
        $uses = $action['uses'] ?? null;
        $controller = is_string($action['controller'] ?? null) ? $action['controller'] : '';

        if (request()->hasHeader('X-Livewire')) {
            try {
                $component = request('components')[0];
                $name = json_decode($component['snapshot'], true)['memo']['name'];
                $method = $component['calls'][0]['method'];
                $class = app(\Livewire\Mechanisms\ComponentRegistry::class)->getClass($name);
                if (class_exists($class) && method_exists($class, $method)) {
                    $controller = $class . '@' . $method;
                    $result['controller'] = ltrim($controller, '\\');
                }
            } catch (\Throwable $e) {
                //
            }
        }

        if (str_contains($controller, '@')) {
            [$controller, $method] = explode('@', $controller);
            if (class_exists($controller) && method_exists($controller, $method)) {
                $reflector = new \ReflectionMethod($controller, $method);
            }
            unset($result['uses']);
        } elseif ($uses instanceof \Closure) {
            $reflector = new \ReflectionFunction($uses);
            $result['uses'] = $this->getDataFormatter()->formatVar($uses);
        } elseif (is_string($uses) && str_contains($uses, '@__invoke')) {
            if (class_exists($controller) && method_exists($controller, 'render')) {
                $reflector = new \ReflectionMethod($controller, 'render');
                $result['controller'] = $controller . '@render';
            }
        }

        if (isset($reflector)) {
            $filename = $this->normalizeFilePath($reflector->getFileName());
            $result['file'] = sprintf('%s:%s-%s', $filename, $reflector->getStartLine(), $reflector->getEndLine());

            if ($link = $this->getXdebugLink($reflector->getFileName(), $reflector->getStartLine())) {
                $result['file'] = [
                    'value' => $result['file'],
                    'xdebug_link' => $link,
                ];

                if (isset($result['controller']) && is_string($result['controller'])) {
                    $result['controller'] = [
                        'value' => $result['controller'],
                        'xdebug_link' => $link,
                    ];
                }
            }
        }

        if (isset($result['middleware']) && is_array($result['middleware'])) {
            $middleware = implode(', ', $result['middleware']);
            unset($result['middleware']);
            $result['middleware'] = $middleware;
        }

        return array_filter($result);
    }
}
