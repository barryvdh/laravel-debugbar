<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\TimeDataCollector;

/**
 * Collects data about SQL statements executed with PDO
 */
class QueryCollector extends PDOCollector
{
    protected $timeCollector;
    protected $queries = array();
    protected $renderSqlWithParams = false;
    protected $findSource = false;
    protected $explainQuery = false;
    protected $explainTypes = array('SELECT'); // array('SELECT', 'INSERT', 'UPDATE', 'DELETE'); for MySQL 5.6.3+

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
    public function setFindSource($value = true)
    {
        $this->findSource = (bool) $value;
    }

    /**
     * Enable/disable the EXPLAIN queries
     *
     * @param  bool $enabled
     * @param  array|null $types Array of types to explain queries (select/insert/update/delete)
     */
    public function setExplainSource($enabled, $types)
    {
        $this->explainQuery = $enabled;
        if($types){
            $this->explainTypes = $types;
        }
    }

    /**
     *
     * @param string $query
     * @param array $bindings
     * @param float $time
     * @param \Illuminate\Database\Connection $connection
     */
    public function addQuery($query, $bindings, $time, $connection)
    {
        $explainResults = array();
        $time = $time / 1000;
        $endTime = microtime(true);
        $startTime = $endTime - $time;
        $hints = $this->performQueryAnalysis($query);

        $pdo = $connection->getPdo();
        $bindings = $connection->prepareBindings($bindings);

        // Run EXPLAIN on this query (if needed)
        if ($this->explainQuery && preg_match('/^('.implode($this->explainTypes).') /i', $query)) {
            $statement = $pdo->prepare('EXPLAIN ' . $query);
            $statement->execute($bindings);
            $explainResults = $statement->fetchAll(\PDO::FETCH_CLASS);
        }

        $bindings = $this->checkBindings($bindings);
        if (!empty($bindings) && $this->renderSqlWithParams) {
            foreach ($bindings as $binding) {
                $query = preg_replace('/\?/', $pdo->quote($binding), $query, 1);
            }
        }

        $source = null;
        if ($this->findSource) {
            try {
                $source = $this->findSource();
            } catch (\Exception $e) {
            }
        }

        $this->queries[] = array(
            'query' => $query,
            'bindings' => $this->escapeBindings($bindings),
            'time' => $time,
            'source' => $source,
            'explain' => $explainResults,
            'hints' => $hints,
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
            if (is_string($binding) && !mb_check_encoding($binding, 'UTF-8')) {
                $binding = '[BINARY DATA]';
            }
        }
        return $bindings;
    }

    /**
     * Make the bindings safe for outputting.
     *
     * @param array $bindings
     * @return array
     */
    protected function escapeBindings($bindings)
    {
        foreach ($bindings as &$binding) {
            $binding = htmlentities($binding, ENT_QUOTES, 'UTF-8', false);
        }
        return $bindings;
    }

    /**
     * Explainer::performQueryAnalysis()
     *
     * Perform simple regex analysis on the code
     *
     * @package xplain (https://github.com/rap2hpoutre/mysql-xplain-xplain)
     * @author e-doceo
     * @copyright 2014
     * @version $Id$
     * @access public
     * @param string $query
     * @return string
     */
    protected function performQueryAnalysis($query)
    {
        $hints = array();
        if (preg_match('/^\\s*SELECT\\s*`?[a-zA-Z0-9]*`?\\.?\\*/i', $query)) {
            $hints[] = 'Use <code>SELECT *</code> only if you need all columns from table';
        }
        if (preg_match('/ORDER BY RAND()/i', $query)) {
            $hints[] = '<code>ORDER BY RAND()</code> is slow, try to avoid if you can.
				You can <a href="http://stackoverflow.com/questions/2663710/how-does-mysqls-order-by-rand-work" target="_blank">read this</a>
				or <a href="http://stackoverflow.com/questions/1244555/how-can-i-optimize-mysqls-order-by-rand-function" target="_blank">this</a>';
        }
        if (strpos($query, '!=') !== false) {
            $hints[] = 'The <code>!=</code> operator is not standard. Use the <code>&lt;&gt;</code> operator to test for inequality instead.';
        }
        if (stripos($query, 'WHERE') === false && preg_match('/^(SELECT) /i', $query)) {
            $hints[] = 'The <code>SELECT</code> statement has no <code>WHERE</code> clause and could examine many more rows than intended';
        }
        if (preg_match('/LIMIT\\s/i', $query) && stripos($query, 'ORDER BY') === false) {
            $hints[] = '<code>LIMIT</code> without <code>ORDER BY</code> causes non-deterministic results, depending on the query execution plan';
        }
        if (preg_match('/LIKE\\s[\'"](%.*?)[\'"]/i', $query, $matches)) {
            $hints[] = 	'An argument has a leading wildcard character: <code>' . $matches[1]. '</code>.
								The predicate with this argument is not sargable and cannot use an index if one exists.';
        }
        return implode("<br />", $hints);
    }
    
    /**
     * Use a backtrace to search for the origin of the query.
     */
    protected function findSource()
    {
        $traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT);
        foreach ($traces as $trace) {
            if (isset($trace['class']) && isset($trace['file']) && strpos(
                    $trace['file'],
                    DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR
                ) === false
            ) {
                if (isset($trace['object']) && is_a($trace['object'], 'Twig_Template')) {
                    list($file, $line) = $this->getTwigInfo($trace);
                } elseif (strpos($trace['file'], storage_path()) !== false) {
                    return 'Template file';
                } else {
                    $file = $trace['file'];
                    $line = isset($trace['line']) ? $trace['line'] : '?';
                }

                return $this->normalizeFilename($file) . ':' . $line;
            } elseif (isset($trace['function']) && $trace['function'] == 'Illuminate\Routing\{closure}') {
                return 'Route binding';
            }
        }
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
     * Shorten the path by removing the relative links and base dir
     *
     * @param string $path
     * @return string
     */
    protected function normalizeFilename($path)
    {
        if (file_exists($path)) {
            $path = realpath($path);
        }
        return str_replace(base_path(), '', $path);
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $totalTime = 0;
        $queries = $this->queries;

        $statements = array();
        foreach ($queries as $query) {
            $totalTime += $query['time'];

            $bindings = $query['bindings'];
            if($query['hints']){
                $bindings['hints'] = $query['hints'];
            }

            $statements[] = array(
                'sql' => $this->formatSql($query['query']),
                'params' => (object) $bindings,
                'duration' => $query['time'],
                'duration_str' => $this->formatDuration($query['time']),
                'stmt_id' => $query['source'],
            );

            //Add the results from the explain as new rows
            foreach($query['explain'] as $explain){
                $statements[] = array(
                    'sql' => ' - EXPLAIN #' . $explain->id . ': `' . $explain->table . '` (' . $explain->select_type . ')',
                    'params' => $explain,
                    'row_count' => $explain->rows,
                    'stmt_id' => $explain->id,
                );
            }
        }

        $data = array(
            'nb_statements' => count($queries),
            'nb_failed_statements' => 0,
            'accumulated_duration' => $totalTime,
            'accumulated_duration_str' => $this->formatDuration($totalTime),
            'statements' => $statements
        );
        return $data;
    }

    /**
     * Removes extra spaces at the beginning and end of the SQL query and its lines.
     *
     * @param string $sql
     * @return string
     */
    protected function formatSql($sql)
    {
        return trim(preg_replace("/\s*\n\s*/", "\n", $sql));
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
