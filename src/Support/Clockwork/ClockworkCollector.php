<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\Support\Clockwork;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * Based on \Symfony\Component\HttpKernel\DataCollector\RequestDataCollector by Fabien Potencier <fabien@symfony.com>
 *
 */
class ClockworkCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    protected Request $request;
    protected Response $response;

    public function __construct(
        Request $request,
        Response $response
    ) {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'clockwork';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function collect(): array
    {
        $request = $this->request;
        $response = $this->response;

        $data = [
            'getData' => $request->query->all(),
            'postData' => $request->request->all(),
            'headers' => $request->headers->all(),
            'cookies' => $request->cookies->all(),
            'uri' => $request->getRequestUri(),
            'method' => $request->getMethod(),
            'responseStatus' => $response->getStatusCode(),
        ];

        if ($this->request->hasSession()) {
            $data['sessionData'] = $this->request->getSession()->all();
        }

        if (isset($data['headers']['authorization'][0])) {
            $data['headers']['authorization'][0] = substr($data['headers']['authorization'][0], 0, 12) . '******';
        }

        return $this->hideMaskedValues($data);
    }
}
