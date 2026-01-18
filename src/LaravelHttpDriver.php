<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar;

use DebugBar\HttpDriverInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class LaravelHttpDriver implements HttpDriverInterface
{

    public function __construct(protected Request $request, protected ?Response $response = null)
    {
    }

    public function setRequest(Request $request): void
    {
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
        $value = $this->request->hasCookie($name);

        return !is_null($value);
    }

    /**
     * {@inheritDoc}
     */
    public function getSessionValue(string $name): mixed
    {
        $value = $this->request->cookie($name);
        if (is_null($value)) {
            return null;
        }

        return json_decode($value, true);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteSessionValue(string $name): void
    {
        $this->setSessionValue($name, null);
    }
}
