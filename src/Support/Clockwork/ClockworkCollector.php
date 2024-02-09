<?php

namespace Barryvdh\Debugbar\Support\Clockwork;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * Based on \Symfony\Component\HttpKernel\DataCollector\RequestDataCollector by Fabien Potencier <fabien@symfony.com>
 *
 */
class ClockworkCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    /** @var \Symfony\Component\HttpFoundation\Request $request */
    protected $request;
    /** @var  \Symfony\Component\HttpFoundation\Request $response */
    protected $response;
    /** @var  \Symfony\Component\HttpFoundation\Session\SessionInterface $session */
    protected $session;
    /** @var array */
    protected $hiddens;

    /**
     * Create a new SymfonyRequestCollector
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Request $response
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param array $hiddens
     */
    public function __construct($request, $response, $session = null, $hiddens = [])
    {
        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
        $this->hiddens = array_merge($hiddens, [
            'request_request.password',
            'request_request.PHP_AUTH_PW',
            'request_request.php-auth-pw',
            'request_headers.php-auth-pw.0',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'clockwork';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
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

        if ($this->session) {
            $data['sessionData'] = $this->session->all();
        }

        if (isset($data['headers']['authorization'][0])) {
            $data['headers']['authorization'][0] = substr($data['headers']['authorization'][0], 0, 12) . '******';
        }

        $keyAlias = [
            'request_query' => 'getData',
            'request_request' => 'postData',
            'request_headers' => 'headers',
            'request_cookies' => 'cookies',
            'session_attributes' => 'sessionData',
        ];
        foreach ($this->hiddens as $key) {
            $key = explode('.', $key);
            $key[0] = $keyAlias[$key[0]] ?? $key[0];
            $key = implode('.', $key);
            if (Arr::has($data, $key)) {
                Arr::set($data, $key, '******');
            }
        }

        return $data;
    }
}
