<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar;

use DebugBar\HttpDriverInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class LaravelHttpDriver implements HttpDriverInterface
{
    protected $cookieValues = [];

    public function __construct(protected Request $request, protected ?Response $response = null) {}

    public function setRequest(Request $request): void
    {
        $this->cookieValues = [];
        $this->request = $request;
    }

    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }

    public function setHeaders(array $headers): void
    {
        if (!is_null($this->response)) {
            $this->response->headers->add($headers);
        }
    }

    public function output(string $content): void
    {
        if (!is_null($this->response)) {
            $existingContent = $this->response->getContent();
            $content = $existingContent ? $existingContent . $content : $content;
            $this->response->setContent($content);
        }
    }

    public function isSessionStarted(): bool
    {
        return true;
    }

    public function setSessionValue(string $name, mixed $value): void
    {
        $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $cookie = Cookie::make($name, $json, 0);
        if ($this->response) {
            $this->response->headers->setCookie($cookie);
        } else {
            Cookie::queue($cookie);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function hasSessionValue(string $name): bool
    {
        $value = $this->getSessionValue($name);

        return !is_null($value);
    }

    /**
     * {@inheritDoc}
     */
    public function getSessionValue(string $name): mixed
    {
        if (array_key_exists($name, $this->cookieValues)) {
            return $this->cookieValues[$name];
        }

        $value = $this->request->cookie($name);
        if ($value !== null) {
            $value = json_decode($value, true);
        }

        $this->cookieValues[$name] = $value;

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteSessionValue(string $name): void
    {
        $this->setSessionValue($name, null);
    }
}
