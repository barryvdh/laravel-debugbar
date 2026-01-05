<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\AssetProvider;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Illuminate\Http\Client\Events\ConnectionFailed;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;

class HttpClientCollector extends DataCollector implements DataCollectorInterface, Renderable, AssetProvider
{
   /** @var array<ResponseReceived|ConnectionFailed>  */
    protected array $events = [];

    public function __construct()
    {
        $this->addMaskedKeys(['Authorization']);
    }

    public function addEvent(ResponseReceived|ConnectionFailed $event): void
    {
        $this->events[] = $event;
    }

    protected function getLabel(ResponseReceived|ConnectionFailed $event)
    {
        return $event->request->url() . ' #' . spl_object_hash($event);
    }
    /**
     * {@inheritdoc}
     */
    public function collect(): array
    {
        $data = [];
        foreach ($this->events as $event) {

            $headers =  $this->hideMaskedValues($event->request->headers());
            if ($event->request->isMultipart()) {
                $input = '[MULTIPART]';
            } else{
                $input = $this->hideMaskedValues($event->request->data());
            }

            $result = [
                'method' => $event->request->method(),
                'url' => $event->request->url(),
                'status' => 'N/A',
                'params' => [
                    'input' => $this->getVarDumper()->renderVar($input),
                    'headers' => $this->getVarDumper()->renderVar($headers),
                ]
            ];
            if ($event instanceof ResponseReceived) {
                $result['status'] = $event->response->status();
                $result['duration'] = $this->getDuration($event->response);
                $result['params']['response'] = $this->getVarDumper()->renderVar($this->parseResponse($event->response));
                $result['params']['headers'] = $this->getVarDumper()->renderVar($this->hideMaskedValues($event->response->headers()));
            } elseif ($event instanceof ConnectionFailed) {
                $result['params']['exception'] = $this->getVarDumper()->renderVar($event->exception);
            }

            $data[] = $result;
        }

        return [
            'data' => $data,
            'nb_events' => count($data),
        ];
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

        if (empty($content)) {
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
        if (property_exists($response, 'transferStats') &&
            $response->transferStats &&
            $response->transferStats->getTransferTime()) {
            return round($response->transferStats->getTransferTime() * 1000);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'http_client';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets(): array
    {
        return [
            "http_client" => [
                "icon" => "flag",
                "widget" => "PhpDebugBar.Widgets.LaravelHttpWidget",
                "map" => "http_client.data",
                "default" => "{}",
            ],
            'http_client:badge' => [
                'map' => 'http_client.nb_events',
                'default' => 0,
            ],
        ];
    }

    public function getAssets(): array
    {
        return [
            'js' => __DIR__ . '/../../resources/http/widget.js',
        ];
    }
}
