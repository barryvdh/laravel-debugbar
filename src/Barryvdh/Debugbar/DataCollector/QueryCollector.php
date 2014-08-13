<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DataCollector\PDO\PDOCollector;

/**
 * Collects data about SQL statements executed with PDO
 */
class QueryCollector extends PDOCollector
{
    protected $timeCollector;
    protected $queries = array();
    protected $renderSqlWithParams = false;
    protected $findSource = false;

    /**
     * @param TimeDataCollector $timeCollector
     */
    public function __construct(TimeDataCollector $timeCollector = null)
    {
        $this->timeCollector = $timeCollector;
    }

    /**
     * Renders the SQL of traced statements with params embeded
     *
     * @param boolean $enabled
     * @param string $quotationChar NOT USED
     */
    public function setRenderSqlWithParams($enabled = true, $quotationChar = "'")
    {
        $this->renderSqlWithParams = $enabled;
    }

    /**
     * Enable/disable finding the source
     *
     * @param bool $value
     */
    public function setFindSource($value = true){
        $this->findSource = (bool) $value;
    }

    /**
     *
     * @param string  $query
     * @param array  $bindings
     * @param float  $time
     * @param \Illuminate\Database\Connection $connection
     */
    public function addQuery($query, $bindings, $time, $connection)
    {

        $time = $time / 1000;
        $endTime = microtime(true);
        $startTime = $endTime - $time;

        $pdo = $connection->getPdo();
        $bindings = $connection->prepareBindings($bindings);
        $bindings = $this->checkBindings($bindings);
        if(!empty($bindings) && $this->renderSqlWithParams){
            foreach($bindings as $binding){
                $query = preg_replace('/\?/', $pdo->quote($binding), $query, 1);
            }
        }

        $source = null;
        if($this->findSource){
            try{
                $source = $this->findSource();
            }catch(\Exception $e){}
        }

        $this->queries[] = array(
            'query' => $query,
            'bindings' => $bindings,
            'time' => $time,
            'source' => $source,
        );

        if ($this->timeCollector !== null) {
            $this->timeCollector->addMeasure($query, $startTime, $endTime);
        }
    }

    /**
     * Use a backtrace to search for the origin of the query.
     */
    protected function findSource()
    {
        $traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT);
        foreach ($traces as $trace) {
            if (isset($trace['class']) && isset($trace['file'])  && strpos($trace['file'], DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) === false) {

                if (isset($trace['object']) && is_a($trace['object'], 'Twig_Template')) {
                    list($file, $line) = $this->getTwigInfo($trace);
                } elseif(strpos($trace['file'], storage_path()) !== false) {
                    return 'Template file';
                } else {
                    $file = $trace['file'];
                    $line = isset($trace['line']) ? $trace['line'] : '?';
                }

                return $this->normalizeFilename($file) . ':' . $line;
            } elseif (isset($trace['function']) && $trace['function'] == 'Illuminate\Routing\{closure}'){
                return 'Route binding';
            }
        }
    }

    /**
     * Shorten the path by removing the relative links and base dir
     *
     * @param string $path
     * @return string
     */
    protected function normalizeFilename($path){
        if(file_exists($path)){
            $path = realpath($path);
        }
        return str_replace(base_path(), '', $path);
    }

    /**
     * Get the filename/line from a Twig template trace
     *
     * @param array $trace
     * @return array The file and line
     */
    protected function getTwigInfo($trace)
    {
        $file = $trace['object']->getTemplateName();

        if (isset($trace['line'])) {
            foreach ($trace['object']->getDebugInfo() as $codeLine => $templateLine) {
                if ($codeLine <= $trace['line']) {
                    return array($file, $templateLine);
                }
            }
        }

        return array($file, -1);
    }

    /**
     * Check bindings for illegal (non UTF-8) strings, like Binary data.
     *
     * @param $bindings
     * @return mixed
     */
    protected function checkBindings($bindings)
    {
        foreach ($bindings as &$binding) {
            if(is_string($binding) && !mb_check_encoding($binding, 'UTF-8')) {
                $binding = '[BINARY DATA]';
            }
        }
        return $bindings;
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $totalTime = 0;
        $queries = $this->queries;

        $statements = array();
        foreach($queries as $query){
            $totalTime += $query['time'];
            $statements[] = array(
                'sql' => $query['query'],
                'params' => (object) $query['bindings'],
                'duration' => $query['time'],
                'duration_str' => $this->formatDuration($query['time']),
                'stmt_id' => $query['source'],
            );
        }

        $data = array(
            'nb_statements' => count($statements),
            'nb_failed_statements' => 0,
            'accumulated_duration' => $totalTime,
            'accumulated_duration_str' => $this->formatDuration($totalTime),
            'statements' => $statements
        );
        return $data;
    }


    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'queries';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return array(
            "queries" => array(
                "icon" => "inbox",
                "widget" => "PhpDebugBar.Widgets.SQLQueriesWidget",
                "map" => "queries",
                "default" => "[]"
            ),
            "queries:badge" => array(
                "map" => "queries.nb_statements",
                "default" => 0
            )
        );
    }
}
