<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\MessagesCollector;
use Psr\Log\LogLevel;
use ReflectionClass;

class LogsCollector extends MessagesCollector
{
    protected $lines = 124;

    public function __construct($path = null, $name = 'logs')
    {
        parent::__construct($name);

        $path = $path ?: $this->getLogsFile();
        $this->getStorageLogs($path);
    }

    /**
     * Get the path to the logs file
     *
     * @return string
     */
    public function getLogsFile()
    {
        return storage_path() . '/logs/laravel.log';
    }

    /**
     * get logs apache in app/storage/logs
     * only 24 last of current day
     *
     * @param string $path
     *
     * @return array
     */
    public function getStorageLogs($path)
    {
        if (!file_exists($path)) {
            return;
        }

        //Load the latest lines, guessing about 15x the number of log entries (for stack traces etc)
        $file = implode("", $this->tailFile($path, $this->lines));

        foreach ($this->getLogs($file) as $log) {
            $this->addMessage($log['header'] . $log['stack'], $log['level'], false);
        }
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
        $text = [];
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

    /**
     * Search a string for log entries
     * Based on https://github.com/mikemand/logviewer/blob/master/src/Kmd/Logviewer/Logviewer.php by mikemand
     *
     * @param $file
     * @return array
     */
    public function getLogs($file)
    {
        $pattern = "/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*/";

        $log_levels = $this->getLevels();

        // There has GOT to be a better way of doing this...
        preg_match_all($pattern, $file, $headings);
        $log_data = preg_split($pattern, $file);

        $log = [];
        foreach ($headings as $h) {
            for ($i = 0, $j = count($h); $i < $j; $i++) {
                foreach ($log_levels as $ll) {
                    if (strpos(strtolower($h[$i]), strtolower('.' . $ll))) {
                        $log[] = ['level' => $ll, 'header' => $h[$i], 'stack' => $log_data[$i]];
                    }
                }
            }
        }

        $log = array_reverse($log);

        return $log;
    }

    /**
     * Get the log levels from psr/log.
     * Based on https://github.com/mikemand/logviewer/blob/master/src/Kmd/Logviewer/Logviewer.php by mikemand
     *
     * @access public
     * @return array
     */
    public function getLevels()
    {
        $class = new ReflectionClass(new LogLevel());
        return $class->getConstants();
    }
}
