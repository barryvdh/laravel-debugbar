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

        $this->queries[] = array(
            'query' => $query,
            'bindings' => $bindings,
            'time' => $time,
        );

        if ($this->timeCollector !== null) {
            $this->timeCollector->addMeasure($query, $startTime, $endTime);
        }
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
