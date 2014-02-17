<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Database\DatabaseManager;


/**
 * Collects data about SQL statements executed with PDO
 */
class QueryCollector extends DataCollector implements Renderable
{
    protected $timeCollector;
    protected $db;
    protected $queries = array();
    protected $renderSqlWithParams = false;

    /**
     * @param DatabaseManager $db
     * @param TimeDataCollector $timeCollector
     */
    public function __construct(DatabaseManager $db, TimeDataCollector $timeCollector = null)
    {
        $this->db = $db;
        $this->timeCollector = $timeCollector;
    }

    /**
     * Renders the SQL of traced statements with params embeded
     *
     * @param boolean $enabled
     */
    public function setRenderSqlWithParams($enabled = true)
    {
        $this->renderSqlWithParams = $enabled;
    }

    public function addQuery($query, $bindings, $time, $connectionName)
    {
        $time = $time / 1000;
        $endTime = microtime(true);
        $startTime = $endTime - $time;

        if(!empty($bindings) && $this->renderSqlWithParams){
            $connection = $this->db->connection($connectionName);
            $pdo = $connection->getPdo();
            $bindings = $connection->prepareBindings($bindings);
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
