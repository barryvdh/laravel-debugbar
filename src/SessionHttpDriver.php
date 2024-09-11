<?php

namespace Barryvdh\Debugbar;

use DebugBar\HttpDriverInterface;
use Illuminate\Session\Store;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * HTTP driver for Larave Request/Session using Cookies
 */
class SessionHttpDriver implements HttpDriverInterface
{
    /** @var \SessionHandlerInterface */
    protected $session;

    /** @var \Symfony\Component\HttpFoundation\Response */
    protected $response;

    protected $data = null;

    protected $id = '_debugbar';

    public function __construct($session, $response = null)
    {
        $this->session = $session;
        $this->response = $response;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    public function setResponse($response)
    {
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

    protected function ensureStarted()
    {
        if ($this->data === null) {
            $this->data = json_decode($this->session->read($this->id), true) ?: [];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isSessionStarted()
    {
        $this->ensureStarted();
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function setSessionValue($name, $value)
    {
        $this->isSessionStarted();

        $this->data[$name] = $value;
        $this->session->write($this->id, json_encode($this->data));
    }

    /**
     * {@inheritDoc}
     */
    public function hasSessionValue($name)
    {
        $this->ensureStarted();

        return array_key_exists($name, $this->data);
    }

    /**
     * {@inheritDoc}
     */
    public function getSessionValue($name)
    {
        $this->ensureStarted();

        return $this->data[$name] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteSessionValue($name)
    {$this->isSessionStarted();

        unset($this->data[$name]);

        $this->session->write($this->id, json_encode($this->data));
    }
}
