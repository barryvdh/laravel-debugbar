<?php
namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\MessagesCollector;

class LogsCollector extends MessagesCollector
{
    //https://github.com/ddtraceweb/monolog-parser/blob/master/src/Dubture/Monolog/Parser/LineLogParser.php
    protected $pattern = '/\[(?P<date>.*)\] (?P<logger>\w+).(?P<level>\w+): (?P<message>[^\[\{]+) (?P<context>[\[\{].*[\]\}]) (?P<extra>[\[\{].*[\]\}])/';
    protected $lines = 24;

    public function __construct($name = 'logs')
    {
        parent::__construct($name);
        $this->getStorageLogs();
    }


    /**
     * get logs apache in app/storage/logs
     * only 24 last of current day
     *
     * @return array
     */
    public function getStorageLogs()
    {

        //Default log location (single file)
        $path = storage_path() . '/logs/laravel.log';

        //Rotating logs (Laravel 4.0)
        if (!file_exists($path)) {
            $path = app_path() . '/storage/logs/log-' . php_sapi_name() . '-' . date('Y-m-d') . '.txt';
        }

        $logs = array();
        if (file_exists($path)) {
            foreach ($this->tailFile($path, $this->lines) as $log) {
                $data = $this->parseLine($log);
                if ($data) {
                    $context = $data['context'];
                    $log = '['.$data['date']->format('Y-m-d H:i:s').'] '. $data['logger'].".".$data['level'].": " . $data['message'] . (!empty($context) ? ' '.print_r($context, true) : '');
                    $this->addMessage($log, $data['level']);
                }
            }
        }

    }


    /**
     * (c) Robert Gruendler <r.gruendler@gmail.com>
     * https://github.com/pulse00/monolog-parser/blob/master/src/Dubture/Monolog/Parser/LineLogParser.php
     * @param $log
     * @return array
     */
    protected function parseLine($log)
    {
        if (!is_string($log) || strlen($log) === 0) {
            return array();
        }

        preg_match($this->pattern, $log, $data);

        if (!isset($data['date'])) {
            return false;
        }

        return array(
            'date' => \DateTime::createFromFormat('Y-m-d H:i:s', $data['date']),
            'logger' => $data['logger'],
            'level' => $data['level'],
            'message' => $data['message'],
            'context' => json_decode($data['context'], true),
            'extra' => json_decode($data['extra'], true),
        );

    }

    /**
     * By Ain Tohvri (ain)
     * http://tekkie.flashbit.net/php/tail-functionality-in-php
     * @param string $file
     * @param int $lines
     * @return array
     */
    protected function tailFile($file, $lines)
    {
        $handle = fopen($file, "r");
        $linecounter = $lines;
        $pos = -2;
        $beginning = false;
        $text = array();
        while ($linecounter > 0) {
            $t = " ";
            while ($t != "\n") {
                if (fseek($handle, $pos, SEEK_END) == -1) {
                    $beginning = true;
                    break;
                }
                $t = fgetc($handle);
                $pos--;
            }
            $linecounter--;
            if ($beginning) {
                rewind($handle);
            }
            $text[$lines - $linecounter - 1] = fgets($handle);
            if ($beginning) {
                break;
            }
        }
        fclose($handle);
        return array_reverse($text);

    }

}