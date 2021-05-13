<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Illuminate\Support\Str;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * Based on \Symfony\Component\HttpKernel\DataCollector\RequestDataCollector by Fabien Potencier <fabien@symfony.com>
 *
 */
class RequestCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    /** @var \Symfony\Component\HttpFoundation\Request $request */
    protected $request;
    /** @var  \Symfony\Component\HttpFoundation\Request $response */
    protected $response;
    /** @var  \Symfony\Component\HttpFoundation\Session\SessionInterface $session */
    protected $session;
    /** @var string|null */
    protected $currentRequestId;

    /**
     * Create a new SymfonyRequestCollector
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     */
    public function __construct($request, $response, $session = null, $currentRequestId = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
        $this->currentRequestId = $currentRequestId;
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
                "widget" => "PhpDebugBar.Widgets.HtmlVariableListWidget",
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
            'path_info' => $request->getPathInfo(),
            'status_code' => $statusCode,
            'status_text' => isset(Response::$statusTexts[$statusCode]) ? Response::$statusTexts[$statusCode] : '',
            'format' => $request->getRequestFormat(),
            'content_type' => $response->headers->get('Content-Type') ? $response->headers->get(
                'Content-Type'
            ) : 'text/html',
            'request_query' => $request->query->all(),
            'request_request' => $request->request->all(),
            'request_headers' => $request->headers->all(),
            'request_server' => $request->server->all(),
            'request_cookies' => $request->cookies->all(),
            'response_headers' => $responseHeaders,
        ];

        if ($this->session) {
            $sessionAttributes = [];
            foreach ($this->session->all() as $key => $value) {
                $sessionAttributes[$key] = $value;
            }
            $data['session_attributes'] = $sessionAttributes;
        }

        foreach ($data['request_server'] as $key => $value) {
            if (
                Str::is('*_KEY', $key) || Str::is('*_PASSWORD', $key)
                    || Str::is('*_SECRET', $key) || Str::is('*_PW', $key)
                    || Str::is('*_TOKEN', $key) || Str::is('*_PASS', $key)
            ) {
                $data['request_server'][$key] = '******';
            }
        }

        if (isset($data['request_headers']['php-auth-pw'])) {
            $data['request_headers']['php-auth-pw'] = '******';
        }

        if (isset($data['request_server']['PHP_AUTH_PW'])) {
            $data['request_server']['PHP_AUTH_PW'] = '******';
        }
        ;

        foreach ($data as $key => $var) {
            if (!is_string($data[$key])) {
                $data[$key] = DataCollector::getDefaultVarDumper()->renderVar($var);
            } else {
                $data[$key] = e($data[$key]);
            }
        }

        $htmlData = [];
        if (class_exists(Telescope::class)) {
            $entry = IncomingEntry::make([
                'requestId' => $this->currentRequestId,
            ])->type('debugbar');
            Telescope::$entriesQueue[] = $entry;
            $url = route('debugbar.telescope', [$entry->uuid]);
            $htmlData['telescope'] = '<a href="' . $url . '" target="_blank">View in Telescope</a>';
        }

        return $htmlData + $data;
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
