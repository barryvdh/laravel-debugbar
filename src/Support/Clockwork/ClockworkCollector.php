<?php

namespace Barryvdh\Debugbar\Support\Clockwork;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
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

    /**
     * Create a new SymfonyRequestCollector
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Request $response
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     */
    public function __construct($request, $response, $session = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
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
            $sessionAttributes = [];
            foreach ($this->session->all() as $key => $value) {
                $sessionAttributes[$key] = $value;
            }
            $data['sessionData'] = $sessionAttributes;
        }

        if (isset($data['postData']['php-auth-pw'])) {
            $data['postData']['php-auth-pw'] = '******';
        }

        if (isset($data['postData']['PHP_AUTH_PW'])) {
            $data['postData']['PHP_AUTH_PW'] = '******';
        }

        return $data;
    }
}
