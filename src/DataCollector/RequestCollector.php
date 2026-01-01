<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * Based on \Symfony\Component\HttpKernel\DataCollector\RequestDataCollector by Fabien Potencier <fabien@symfony.com>
 *
 */
class RequestCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    protected Request $request;
    protected Response $response;
    protected ?SessionManager $session;
    protected ?string $currentRequestId = null;
    protected array $hiddens = [];

    public function __construct(
        Request $request,
        Response $response,
        ?SessionManager $session = null,
        ?string $currentRequestId = null,
        array $hiddens = []
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
        $this->currentRequestId = $currentRequestId;
        $this->hiddens = array_merge($hiddens, [
            'request_request.password',
            'request_headers.php-auth-pw.0',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'request';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets(): array
    {
        $widgets = [
            "request" => [
                "icon" => "tags",
                "widget" => "PhpDebugBar.Widgets.HtmlVariableListWidget",
                "map" => "request.data",
                "order" => -100,
                "default" => "{}",
            ],
            'request:badge' => [
                "map" => "request.badge",
                "default" => "null",
            ],
        ];

        if (Config::get('debugbar.options.request.label', true)) {
            $widgets['currentrequest'] = [
                "icon" => "share-3",
                "map" => "request.data.uri",
                "link" => "request",
                "default" => "",
            ];
            $widgets['currentrequest:tooltip'] = [
                "map" => "request.tooltip",
                "default" => "{}",
            ];
        }

        return $widgets;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(): array
    {
        $request = $this->request;
        $response = $this->response;

        $responseHeaders = $response->headers->all();
        $cookies = [];
        foreach ($response->headers->getCookies() as $cookie) {
            $cookies[] = $this->getCookieHeader(
                $cookie->getName(),
                $cookie->getValue(),
                $cookie->getExpiresTime(),
                $cookie->getPath(),
                $cookie->getDomain(),
                $cookie->isSecure(),
                $cookie->isHttpOnly(),
            );
        }
        if (count($cookies) > 0) {
            $responseHeaders['Set-Cookie'] = $cookies;
        }

        $statusCode = $response->getStatusCode();
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : $request->server->get('REQUEST_TIME_FLOAT');
        $query = $request->getQueryString();
        $htmlData = [];

        $data = [
            'status' => $statusCode . ' ' . (Response::$statusTexts[$statusCode] ?? ''),
            'duration' => $startTime ? $this->getDataFormatter()->formatDuration(microtime(true) - $startTime) : null,
            'peak_memory' => $this->getDataFormatter()->formatBytes(memory_get_peak_usage(true), 1),
        ];

        if ($request instanceof \Illuminate\Http\Request) {

            if ($route = $request->route()) {
                $htmlData += $this->getRouteInformation($route);
            }

            $fullUrl = $request->fullUrl();
            $data += [
                'full_url' => strlen($fullUrl) > 100 ? [$fullUrl] : $fullUrl,
            ];
        }

        if ($response instanceof RedirectResponse) {
            $data['response'] = 'Redirect to ' . $response->getTargetUrl();
        }

        $data += [
            'response' => $response->headers->get('Content-Type') ? $response->headers->get(
                'Content-Type',
            ) : 'text/html',
            'request_format' => $request->getRequestFormat(),
            'request_query' => $request->query->all(),
            'request_request' => $request->request->all(),
            'request_headers' => $request->headers->all(),
            'request_cookies' => $request->cookies->all(),
            'response_headers' => $responseHeaders,
        ];

        if ($this->session) {
            $data['session_attributes'] = $this->session->all();
        }

        if (isset($data['request_headers']['authorization'][0])) {
            $data['request_headers']['authorization'][0] = substr($data['request_headers']['authorization'][0], 0, 12) . '******';
        }

        foreach ($this->hiddens as $key) {
            if (Arr::has($data, $key)) {
                Arr::set($data, $key, '******');
            }
        }

        foreach ($data as $key => $var) {
            if (!is_string($data[$key])) {
                $data[$key] = DataCollector::getDefaultVarDumper()->renderVar($var);
            } else {
                $data[$key] = e($data[$key]);
            }
        }

        if (class_exists(Telescope::class)) {
            $entry = IncomingEntry::make([
                'requestId' => $this->currentRequestId,
            ])->type('debugbar');
            Telescope::$entriesQueue[] = $entry;
            $url = route('debugbar.telescope', [$entry->uuid]);
            $htmlData['telescope'] = '<a href="' . $url . '" target="_blank">View in Telescope</a>';
        }

        $tooltip = [
            'status' => $data['status'],
        ];

        if ($this->request instanceof \Illuminate\Http\Request) {
            $tooltip += [
                'full_url' => Str::limit($this->request->fullUrl(), 100),
                'action_name' => $this->request->route()?->getName(),
                'controller_action' => $this->request->route()?->getActionName(),
            ];
        }

        unset($htmlData['as'], $htmlData['uses']);

        return [
            'data' => $tooltip + $htmlData + $data,
            'tooltip' => array_filter($tooltip),
            'badge' => $statusCode >= 300 ? $data['status'] : null,
        ];
    }

    protected function getRouteInformation(mixed $route): array
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

    protected function getCookieHeader(string $name, ?string $value, int $expires, string $path, ?string $domain, bool $secure, bool $httponly): string
    {
        $cookie = sprintf('%s=%s', $name, urlencode($value ?? ''));

        if (0 !== $expires) {
            if (is_numeric($expires)) {
                $expires = (int) $expires;
            } elseif ($expires instanceof \DateTime) {
                $expires = $expires->getTimestamp();
            } else {
                $expires = strtotime((string) $expires);
                if (false === $expires || -1 == $expires) {
                    throw new \InvalidArgumentException('The "expires" cookie parameter is not valid.');
                }
            }

            $cookie .= '; expires=' . substr(
                \DateTime::createFromFormat('U', (string) $expires, new \DateTimeZone('UTC'))->format('D, d-M-Y H:i:s T'),
                0,
                -5,
            );
        }

        if ($domain) {
            $cookie .= '; domain=' . $domain;
        }

        $cookie .= '; path=' . $path;

        if ($secure) {
            $cookie .= '; secure';
        }

        if ($httponly) {
            $cookie .= '; httponly';
        }

        return $cookie;
    }
}
