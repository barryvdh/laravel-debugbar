<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * Based on \Symfony\Component\HttpKernel\DataCollector\RequestDataCollector by Fabien Potencier <fabien@symfony.com>
 *
 */
class RequestCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    /** @var \Symfony\Component\HttpFoundation\Request $request */
    protected $request;
    /** @var  \Symfony\Component\HttpFoundation\Response $response */
    protected $response;
    /** @var  \Symfony\Component\HttpFoundation\Session\SessionInterface $session */
    protected $session;
    /** @var string|null */
    protected $currentRequestId;
    /** @var array */
    protected $hiddens;

    /**
     * Create a new SymfonyRequestCollector
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param string|null $currentRequestId
     * @param array $hiddens
     */
    public function __construct($request, $response, $session = null, $currentRequestId = null, $hiddens = [])
    {
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
    public function getName()
    {
        return 'request';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        $widgets = [
            "request" => [
                "icon" => "tags",
                "widget" => "PhpDebugBar.Widgets.HtmlVariableListWidget",
                "map" => "request.data",
                "order" => -100,
                "default" => "{}"
            ],
            'request:badge' => [
                "map" => "request.badge",
                "default" => "null"
            ]
        ];

        if (Config::get('debugbar.options.request.label', true)) {
            $widgets['currentrequest'] = [
                "icon" => "share",
                "map" => "request.data.uri",
                "link" => "request",
                "default" => ""
            ];
            $widgets['currentrequest:tooltip'] = [
                "map" => "request.tooltip",
                "default" => "{}"
            ];
        }

        return $widgets;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
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
                $cookie->isHttpOnly()
            );
        }
        if (count($cookies) > 0) {
            $responseHeaders['Set-Cookie'] = $cookies;
        }

        $statusCode = $response->getStatusCode();
        $startTime = defined('LARAVEL_START') ? LARAVEL_START :  $request->server->get('REQUEST_TIME_FLOAT');
        $query = $request->getQueryString();
        $htmlData = [];

        $data = [
            'status' => $statusCode . ' ' . (isset(Response::$statusTexts[$statusCode]) ? Response::$statusTexts[$statusCode] : ''),
            'duration' => $startTime ? $this->formatDuration(microtime(true) - $startTime) : null,
            'peak_memory' => $this->formatBytes(memory_get_peak_usage(true), 1),
        ];

        if ($request instanceof Request) {

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
                'Content-Type'
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
            'full_url' => Str::limit($request->fullUrl(), 100),
        ];

        if ($this->request instanceof Request) {
            $tooltip += [
                'action_name' => optional($this->request->route())->getName(),
                'controller_action' => optional($this->request->route())->getActionName(),
            ];
        }

        unset($htmlData['as'], $htmlData['uses']);

        return [
            'data' => $tooltip + $htmlData + $data,
            'tooltip' => array_filter($tooltip),
            'badge' => $statusCode >= 300 ? $data['status'] : null,
        ];
    }

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
        $uses = $action['uses'] ?? null;
        $controller = is_string($action['controller'] ?? null) ? $action['controller'] :  '';

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
            list($controller, $method) = explode('@', $controller);
            if (class_exists($controller) && method_exists($controller, $method)) {
                $reflector = new \ReflectionMethod($controller, $method);
            }
            unset($result['uses']);
        } elseif ($uses instanceof \Closure) {
            $reflector = new \ReflectionFunction($uses);
            $result['uses'] = $this->formatVar($uses);
        } elseif (is_string($uses) && str_contains($uses, '@__invoke')) {
            if (class_exists($controller) && method_exists($controller, 'render')) {
                $reflector = new \ReflectionMethod($controller, 'render');
                $result['controller'] = $controller . '@render';
            }
        }

        if (isset($reflector)) {
            $filename = $this->normalizeFilePath($reflector->getFileName());

            if ($link = $this->getXdebugLink($reflector->getFileName(), $reflector->getStartLine())) {
                $result['file'] = sprintf(
                    '<a href="%s" onclick="%s" class="phpdebugbar-widgets-editor-link">%s:%s-%s</a>',
                    $link['url'],
                    $link['ajax'] ? 'event.preventDefault();$.ajax(this.href);' : '',
                    $filename,
                    $reflector->getStartLine(),
                    $reflector->getEndLine()
                );

                if (isset($result['controller']) && is_string($result['controller'])) {
                    $result['controller'] .= '<a href="'.$link['url'].'" class="phpdebugbar-widgets-editor-link"></a>';
                }
            } else {
                $result['file'] = sprintf('%s:%s-%s', $filename, $reflector->getStartLine(), $reflector->getEndLine());
            }
        }

        if (isset($result['middleware']) && is_array($result['middleware'])) {
            $middleware = implode(', ', $result['middleware']);
            unset($result['middleware']);
            $result['middleware'] = $middleware;
        }

        return array_filter($result);
    }

    private function getCookieHeader($name, $value, $expires, $path, $domain, $secure, $httponly)
    {
        $cookie = sprintf('%s=%s', $name, urlencode($value ?? ''));

        if (0 !== $expires) {
            if (is_numeric($expires)) {
                $expires = (int) $expires;
            } elseif ($expires instanceof \DateTime) {
                $expires = $expires->getTimestamp();
            } else {
                $expires = strtotime($expires);
                if (false === $expires || -1 == $expires) {
                    throw new \InvalidArgumentException(
                        sprintf('The "expires" cookie parameter is not valid.', $expires)
                    );
                }
            }

            $cookie .= '; expires=' . substr(
                    \DateTime::createFromFormat('U', $expires, new \DateTimeZone('UTC'))->format('D, d-M-Y H:i:s T'),
                    0,
                    -5
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
