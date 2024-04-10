<?php

namespace Barryvdh\Debugbar;

use DebugBar\HttpDriverInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * HTTP driver for Symfony Request/Session
 */
class SymfonyHttpDriver implements HttpDriverInterface
{
    /** @var \Illuminate\Contracts\Session\Session|\Illuminate\Session\SessionManager */
    protected $session;

    /** @var \Symfony\Component\HttpFoundation\Response */
    protected $response;

    public function __construct($session, $response = null)
    {
        $this->session = $session;
        $this->response = $response;
    }

    /**
     * {@inheritDoc}
     */
    public function setHeaders(array $headers)
    {
        if (!is_null($this->response)) {
            $this->response->headers->add($headers);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isSessionStarted()
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        return $this->session->isStarted();
    }

    /**
     * {@inheritDoc}
     */
    public function setSessionValue($name, $value)
    {
        $this->session->put($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function hasSessionValue($name)
    {
        return $this->session->has($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getSessionValue($name)
    {
        return $this->session->get($name);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteSessionValue($name)
    {
        $this->session->remove($name);
    }
}
