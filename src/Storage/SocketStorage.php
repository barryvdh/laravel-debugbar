<?php

declare(strict_types=1);

namespace Barryvdh\Debugbar\Storage;

use DebugBar\Storage\StorageInterface;

class SocketStorage implements StorageInterface
{
    protected string $hostname;
    protected int $port;
    protected object $socket;

    /**
     * @param string $hostname The hostname to use for the socket
     * @param int    $port     The port to use for the socket
     */
    public function __construct(string $hostname, int $port)
    {
        $this->hostname = $hostname;
        $this->port = $port;
    }

    /**
     * @inheritDoc
     */
    public function save(string $id, array $data): void
    {
        $socketIsFresh = !$this->socket;

        if (!$this->socket = $this->socket ?: $this->createSocket()) {
            return;
        }

        $encodedPayload = json_encode([
            'id' => $id,
            'base_path' => base_path(),
            'app' => config('app.name'),
            'data' => $data,
        ]);

        $encodedPayload = strlen($encodedPayload) . '#' . $encodedPayload;

        set_error_handler([self::class, 'nullErrorHandler']);
        try {
            if (-1 !== stream_socket_sendto($this->socket, $encodedPayload)) {
                return;
            }
            if (!$socketIsFresh) {
                stream_socket_shutdown($this->socket, \STREAM_SHUT_RDWR);
                fclose($this->socket);
                $this->socket = $this->createSocket();
            }
            if (-1 !== stream_socket_sendto($this->socket, $encodedPayload)) {
                return;
            }
        } finally {
            restore_error_handler();
        }
    }

    private static function nullErrorHandler($t, $m): void
    {
        // no-op
    }

    protected function createSocket(): false|object
    {
        set_error_handler([self::class, 'nullErrorHandler']);
        try {
            return stream_socket_client("tcp://{$this->hostname}:{$this->port}");
        } finally {
            restore_error_handler();
        }
    }

    /**
     * @inheritDoc
     */
    public function get(string $id): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function find(array $filters = [], int $max = 20, int $offset = 0): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        //
    }
}
