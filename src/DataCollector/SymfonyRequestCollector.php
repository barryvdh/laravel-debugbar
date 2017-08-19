<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * Based on \Symfony\Component\HttpKernel\DataCollector\RequestDataCollector by Fabien Potencier <fabien@symfony.com>
 *
 */
class SymfonyRequestCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    /** @var \Symfony\Component\HttpFoundation\Request $request */
    protected $request;
    /** @var  \Symfony\Component\HttpFoundation\Request $response */
    protected $response;
    /** @var  \Symfony\Component\HttpFoundation\Session\SessionInterface $session */
    protected $session;

    protected $options;

    /**
     * Create a new SymfonyRequestCollector
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Request $response
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     */
    public function __construct($request, $response, $session = null, $options = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
        $this->options = $options;
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
        return [
            "request" => [
                "icon" => "tags",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "request",
                "default" => "{}"
            ]
        ];
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

        $data = [
            'format' => $request->getRequestFormat(),
            'content_type' => $response->headers->get('Content-Type') ? $response->headers->get(
                'Content-Type'
            ) : 'text/html',
            'status_text' => isset(Response::$statusTexts[$statusCode]) ? Response::$statusTexts[$statusCode] : '',
            'status_code' => $statusCode,
            'request_query' => $request->query->all(),
            'request_request' => $request->request->all(),
            'request_headers' => $request->headers->all(),
            'request_server' => $request->server->all(),
            'request_cookies' => $request->cookies->all(),
            'response_headers' => $responseHeaders,
            'path_info' => $request->getPathInfo(),
        ];

        if ($this->session) {
            $sessionAttributes = [];
            foreach ($this->session->all() as $key => $value) {
                $sessionAttributes[$key] = $value;
            }
            $data['session_attributes'] = $sessionAttributes;
        }

        foreach ($data['request_server'] as $key => $value) {
            if (str_is('*_KEY', $key) || str_is('*_PASSWORD', $key)
                    || str_is('*_SECRET', $key) || str_is('*_PW', $key)) {
                $data['request_server'][$key] = '******';
            }
        }

        if (count($hide = $this->options['hide'])) {
            $data['request_server'] = array_filter($data['request_server'], function ($key) use ($hide) {
                return count(array_filter($hide, function ($pattern) use ($key) {
                    return str_is($pattern, $key);
                }));
            }, ARRAY_FILTER_USE_KEY);
        }

        if (count($secret = $this->options['secrets'])) {
            array_walk($keys, function (&$value, $key) use ($secret) {
                $value = count(array_filter($secret, function ($pattern) use ($key) {
                    return str_is($pattern, $key);
                })) ? '******' : $value;
            });
        }

        if (isset($data['request_headers']['php-auth-pw'])) {
            $data['request_headers']['php-auth-pw'] = '******';
        }

        if (isset($data['request_server']['PHP_AUTH_PW'])) {
            $data['request_server']['PHP_AUTH_PW'] = '******';
        }

        foreach ($data as $key => $var) {
            if (!is_string($data[$key])) {
                $data[$key] = $this->formatVar($var);
            }
        }

        return $data;
    }

    private function getCookieHeader($name, $value, $expires, $path, $domain, $secure, $httponly)
    {
        $cookie = sprintf('%s=%s', $name, urlencode($value));

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
