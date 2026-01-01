<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar;

use DebugBar\HttpDriverInterface;
use Illuminate\Contracts\Session\Session as SessionContract;
use Illuminate\Session\SessionManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * HTTP driver for Symfony Request/Session
 *
 */
class SymfonyHttpDriver implements HttpDriverInterface
{
    protected SessionContract|SessionManager $session;

    protected ?Response $response;

    public function __construct(SessionContract|SessionManager $session, ?Response $response = null)
    {
        $this->session = $session;
        $this->response = $response;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    public function setHeaders(array $headers): void
    {
        if (!is_null($this->response)) {
            $this->response->headers->add($headers);
        }
    }

    public function isSessionStarted(): bool
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        return $this->session->isStarted();
    }

    public function setSessionValue(string $name, mixed $value): void
    {
        $this->session->put($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function hasSessionValue(string $name): bool
    {
        return $this->session->has($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getSessionValue(string $name): mixed
    {
        return $this->session->get($name);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteSessionValue(string $name): void
    {
        $this->session->remove($name);
    }
}
