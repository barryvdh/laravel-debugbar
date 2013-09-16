<?php


namespace Barryvdh\Debugbar;
use DebugBar\DebugBar;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\ExceptionsCollector;
use Barryvdh\Debugbar\DataCollector\LaravelCollector;

/**
 * Debug bar subclass which adds all without Request and with LaravelCollector.
 * Rest is added in Service Provider
 */
class LaravelDebugbar extends DebugBar
{

    public function addDataToHeaders($response, $headerName = 'phpdebugbar', $maxHeaderLength = 4096)
    {

        $headers = array();
        $data = rawurlencode(json_encode(array(
                    'id' => $this->getCurrentRequestId(),
                    'data' => $this->getData()
                )));
        $chunks = array();

        while (strlen($data) > $maxHeaderLength) {
            $chunks[] = substr($data, 0, $maxHeaderLength);
            $data = substr($data, $maxHeaderLength);
        }
        $chunks[] = $data;

        for ($i = 0, $c = count($chunks); $i < $c; $i++) {
            $name = $headerName . ($i > 0 ? "-$i" : '');
            $headers[$name] = $chunks[$i];

        }

        $response->headers->add($headers);
    }

}