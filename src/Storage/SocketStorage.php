<?php

namespace Barryvdh\Debugbar\Storage;

use DebugBar\Storage\StorageInterface;

class SocketStorage implements StorageInterface
{
    protected $hostname;
    protected $port;
    protected $socket;

    /**
     * @param string $hostname The hostname to use for the socket
     * @param int $port The port to use for the socket
     */
    public function __construct($hostname, $port)
    {
        $this->hostname = $hostname;
        $this->port = $port;
    }

    /**
     * @inheritDoc
     */
    function save($id, $data)
    {
        $socketIsFresh = !$this->socket;

        if (!$this->socket = $this->socket ?: $this->createSocket()) {
            return false;
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
                return true;
            }
            if (!$socketIsFresh) {
                stream_socket_shutdown($this->socket, \STREAM_SHUT_RDWR);
                fclose($this->socket);
                $this->socket = $this->createSocket();
            }
            if (-1 !== stream_socket_sendto($this->socket, $encodedPayload)) {
                return true;
            }
        } finally {
            restore_error_handler();
        }
    }

    private static function nullErrorHandler($t, $m)
    {
        // no-op
    }

    protected function createSocket()
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
    function get($id)
    {
        //
    }

    /**
     * @inheritDoc
     */
    function find(array $filters = array(), $max = 20, $offset = 0)
    {
        //
    }

    /**
     * @inheritDoc
     */
    function clear()
    {
        //
    }
}
