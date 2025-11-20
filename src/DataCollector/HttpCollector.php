<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class HttpCollector extends DataCollector implements Renderable
{
    /** @var Request */
    protected $request;

    /** @var Response */
    protected $response;

    /** @var float */
    protected $startTime;

    /** @var array */
    protected $hiddenRequestHeaders;

    /** @var array */
    protected $hiddenParameters;

    /** @var array */
    protected $hiddenResponseParameters;

    /** @var array */
    protected $ignoredStatusCodes;

    /** @var int */
    protected $sizeLimit;

    /**
     * Create a new HttpCollector
     *
     * @param Request $request
     * @param Response $response
     * @param float|null $startTime
     * @param array $hiddenRequestHeaders
     * @param array $hiddenParameters
     * @param array $hiddenResponseParameters
     * @param array $ignoredStatusCodes
     * @param int $sizeLimit Size limit in KB (default: 64)
     */
    public function __construct(
        Request $request,
        Response $response,
        $startTime = null,
        array $hiddenRequestHeaders = [],
        array $hiddenParameters = [],
        array $hiddenResponseParameters = [],
        array $ignoredStatusCodes = [],
        int $sizeLimit = 64
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->startTime = $startTime ?? (defined('LARAVEL_START') ? LARAVEL_START : $request->server->get('REQUEST_TIME_FLOAT'));
        $this->hiddenRequestHeaders = $hiddenRequestHeaders;
        $this->hiddenParameters = $hiddenParameters;
        $this->hiddenResponseParameters = $hiddenResponseParameters;
        $this->ignoredStatusCodes = $ignoredStatusCodes;
        $this->sizeLimit = $sizeLimit;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'http';
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $uri = str_replace($this->request->root(), '', $this->request->fullUrl()) ?: '/';
        $statusCode = $this->response->getStatusCode();
        $method = $this->request->method();

        // Check if status code should be ignored
        if (in_array($statusCode, $this->ignoredStatusCodes)) {
            return [];
        }

        return [
            'title' => "$uri returned HTTP Status Code $statusCode",
            'description' => "$uri for $method request returned HTTP Status Code $statusCode",
            'uri' => $uri,
            'method' => $method,
            'controller_action' => optional($this->request->route())->getActionName(),
            'middleware' => array_values(optional($this->request->route())->gatherMiddleware() ?? []),
            'headers' => $this->formatHeaders($this->request->headers->all()),
            'payload' => $this->formatPayload($this->extractInput($this->request)),
            'session' => $this->formatPayload($this->extractSessionVariables($this->request)),
            'response_status' => $statusCode,
            'response' => $this->formatResponse($this->response),
            'duration' => $this->startTime ? floor((microtime(true) - $this->startTime) * 1000) : null,
            'memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 1),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return [
            'http' => [
                'icon' => 'globe',
                'widget' => 'PhpDebugBar.Widgets.HtmlVariableListWidget',
                'map' => 'http',
                'default' => '{}',
            ],
            'http:badge' => [
                'map' => 'http.response_status',
                'default' => 'null',
            ],
        ];
    }

    /**
     * Format the given headers.
     *
     * @param array $headers
     * @return array
     */
    protected function formatHeaders(array $headers)
    {
        $headers = collect($headers)->map(function ($header) {
            return $header[0] ?? $header;
        })->toArray();

        return $this->hideParameters($headers, $this->hiddenRequestHeaders);
    }

    /**
     * Format the given payload.
     *
     * @param array $payload
     * @return array
     */
    protected function formatPayload(array $payload)
    {
        return $this->hideParameters($payload, $this->hiddenParameters);
    }

    /**
     * Hide the given parameters.
     *
     * @param array $data
     * @param array $hidden
     * @return array
     */
    protected function hideParameters(array $data, array $hidden)
    {
        foreach ($hidden as $parameter) {
            if (Arr::get($data, $parameter)) {
                Arr::set($data, $parameter, '********');
            }
        }

        return $data;
    }

    /**
     * Extract the session variables from the given request.
     *
     * @param Request $request
     * @return array
     */
    protected function extractSessionVariables(Request $request)
    {
        return $request->hasSession() ? $request->session()->all() : [];
    }

    /**
     * Extract the input from the given request.
     *
     * @param Request $request
     * @return array
     */
    protected function extractInput(Request $request)
    {
        $files = $request->files->all();

        array_walk_recursive($files, function (&$file) {
            $file = [
                'name' => $file->getClientOriginalName(),
                'size' => $file->isFile() ? ($file->getSize() / 1000) . 'KB' : '0',
            ];
        });

        return array_replace_recursive($request->input(), $files);
    }

    /**
     * Format the given response object.
     *
     * @param Response $response
     * @return array|string
     */
    protected function formatResponse(Response $response)
    {
        $content = $response->getContent();

        if (is_string($content)) {
            // Handle JSON responses
            if (is_array(json_decode($content, true)) && json_last_error() === JSON_ERROR_NONE) {
                return $this->contentWithinLimits($content)
                    ? $this->hideParameters(json_decode($content, true), $this->hiddenResponseParameters)
                    : 'Purged By Debugbar (Response too large)';
            }

            // Handle plain text responses
            if (Str::startsWith(strtolower($response->headers->get('Content-Type') ?? ''), 'text/plain')) {
                return $this->contentWithinLimits($content) ? $content : 'Purged By Debugbar (Response too large)';
            }
        }

        // Handle redirect responses
        if ($response instanceof RedirectResponse) {
            return 'Redirected to ' . $response->getTargetUrl();
        }

        // Handle view responses
        if ($response instanceof IlluminateResponse && $response->getOriginalContent() instanceof View) {
            return [
                'view' => $response->getOriginalContent()->getPath(),
                'data' => $this->extractDataFromView($response->getOriginalContent()),
            ];
        }

        return 'HTML Response';
    }

    /**
     * Determine if the content is within the set limits.
     *
     * @param string $content
     * @return bool
     */
    protected function contentWithinLimits($content)
    {
        return mb_strlen($content) / 1000 <= $this->sizeLimit;
    }

    /**
     * Extract the data from the given view in array form.
     *
     * @param View $view
     * @return array
     */
    protected function extractDataFromView(View $view)
    {
        return collect($view->getData())->map(function ($value) {
            if ($value instanceof Model) {
                return $this->formatModel($value);
            } elseif (is_object($value)) {
                return [
                    'class' => get_class($value),
                    'properties' => json_decode(json_encode($value), true),
                ];
            } else {
                return json_decode(json_encode($value), true);
            }
        })->toArray();
    }

    /**
     * Format a model instance.
     *
     * @param Model $model
     * @return array
     */
    protected function formatModel(Model $model)
    {
        return [
            'class' => get_class($model),
            'key' => $model->getKey(),
            'attributes' => $model->getAttributes(),
            'relations' => collect($model->getRelations())->map(function ($relation) {
                if ($relation instanceof Model) {
                    return [
                        'class' => get_class($relation),
                        'key' => $relation->getKey(),
                    ];
                }
                return gettype($relation);
            })->toArray(),
        ];
    }
}
