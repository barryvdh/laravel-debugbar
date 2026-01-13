<?php

declare(strict_types=1);

namespace Fruitcake\LaravelDebugbar\DataCollector;

use DebugBar\DataCollector\HttpCollector;
use Illuminate\Http\Client\Events\ConnectionFailed;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;

class HttpClientCollector extends HttpCollector
{
    public function addEvent(ResponseReceived|ConnectionFailed $event): void
    {
        $headers =  $this->hideMaskedValues($event->request->headers());

        if ($event->request->isMultipart()) {
            $requestData = '[MULTIPART]';
        } else {
            $requestData = $this->hideMaskedValues($event->request->data());
        }

        $status = null;
        $duration = null;
        $details = [
            'request_data' => $requestData,
            'request_headers' => $headers,
        ];

        if ($event instanceof ResponseReceived) {
            $status = $event->response->status();
            $duration = $this->getDuration($event->response);
            $details['response'] = $this->parseResponse($event->response);
            $details['response_headers'] = $this->hideMaskedValues($event->response->headers());
        } elseif ($event instanceof ConnectionFailed && isset($event->exception)) {
            $details['exception'] = $event->exception;
        }

        $this->addRequest(
            $event->request->method(),
            $event->request->url(),
            $status,
            $duration,
            $details
        );
    }

    protected function parseResponse(Response $response): string|array
    {
        if ($response->redirect()) {
            return 'Redirect: ' . $response->header('Location');
        }

        // Check if stream
        $stream = $response->toPsrResponse()->getBody();
        if (! $stream->isSeekable()) {
            return '[STREAM]';
        }

        $content = $response->body();
        $stream->rewind();

        if ($content === '') {
            return '[EMPTY]';
        }

        $json = json_decode($content, true);
        if ($json) {
            return $json;
        }

        return Str::limit($content, 1024);
    }

    protected function getDuration(Response $response): ?float
    {
        if (property_exists($response, 'transferStats') && $response->transferStats) {
            return $response->transferStats->getTransferTime();
        }

        return null;
    }
}
