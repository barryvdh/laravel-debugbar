<?php

namespace Barryvdh\Debugbar\DataCollector;

use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Support\Str;

/**
 * Collects data about SQL statements executed with PDO
 */
class QueryCollector extends PDOCollector
{
    protected $timeCollector;
    protected $queries = [];
    protected $queryCount = 0;
    protected $softLimit = null;
    protected $hardLimit = null;
    protected $lastMemoryUsage;
    protected $renderSqlWithParams = false;
    protected $findSource = false;
    protected $middleware = [];
    protected $durationBackground = true;
    protected $explainQuery = false;
    protected $explainTypes = ['SELECT']; // ['SELECT', 'INSERT', 'UPDATE', 'DELETE']; for MySQL 5.6.3+
    protected $showHints = false;
    protected $showCopyButton = false;
    protected $reflection = [];
    protected $backtraceExcludePaths = [
        '/vendor/laravel/framework/src/Illuminate/Support',
        '/vendor/laravel/framework/src/Illuminate/Database',
        '/vendor/laravel/framework/src/Illuminate/Events',
        '/vendor/laravel/framework/src/Illuminate/Collections',
        '/vendor/october/rain',
        '/vendor/barryvdh/laravel-debugbar',
    ];

    /**
     * @param TimeDataCollector $timeCollector
     */
    public function __construct(TimeDataCollector $timeCollector = null)
    {
        $this->timeCollector = $timeCollector;
    }

    /**
     * @param int|null $softLimit After the soft limit, no parameters/backtrace are captured
     * @param int|null $hardLimit After the hard limit, queries are ignored
     * @return void
     */
    public function setLimits(?int $softLimit, ?int $hardLimit): void
    {
        $this->softLimit = $softLimit;
        $this->hardLimit = $hardLimit;
    }

    /**
     * Renders the SQL of traced statements with params embedded
     *
     * @param boolean $enabled
     * @param string $quotationChar NOT USED
     */
    public function setRenderSqlWithParams($enabled = true, $quotationChar = "'")
    {
        $this->renderSqlWithParams = $enabled;
    }

    /**
     * Show or hide the hints in the parameters
     *
     * @param boolean $enabled
     */
    public function setShowHints($enabled = true)
    {
        $this->showHints = $enabled;
    }

    /**
     * Show or hide copy button next to the queries
     *
     * @param boolean $enabled
     */
    public function setShowCopyButton($enabled = true)
    {
        $this->showCopyButton = $enabled;
    }

    /**
     * Enable/disable finding the source
     *
     * @param bool $value
     * @param array $middleware
     */
    public function setFindSource($value, array $middleware)
    {
        $this->findSource = (bool) $value;
        $this->middleware = $middleware;
    }

    /**
     * Set additional paths to exclude from the backtrace
     *
     * @param array $excludePaths Array of file paths to exclude from backtrace
     */
    public function mergeBacktraceExcludePaths(array $excludePaths)
    {
        $this->backtraceExcludePaths = array_merge($this->backtraceExcludePaths, $excludePaths);
    }

    /**
     * Enable/disable the shaded duration background on queries
     *
     * @param  bool $enabled
     */
    public function setDurationBackground($enabled = true)
    {
        $this->durationBackground = $enabled;
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
        // workaround ['SELECT'] only. https://github.com/barryvdh/laravel-debugbar/issues/888
//        if($types){
//            $this->explainTypes = $types;
//        }
    }

    public function startMemoryUsage()
    {
        $this->lastMemoryUsage = memory_get_usage(false);
    }

    /**
     *
     * @param \Illuminate\Database\Events\QueryExecuted $query
     */
    public function addQuery($query)
    {
        $this->queryCount++;

        if ($this->hardLimit && $this->queryCount > $this->hardLimit) {
            return;
        }

        $limited = $this->softLimit && $this->queryCount > $this->softLimit;

        $sql = (string) $query->sql;
        $explainResults = [];
        $time = $query->time / 1000;
        $endTime = microtime(true);
        $startTime = $endTime - $time;
        $hints = $this->performQueryAnalysis($sql);

        $pdo = null;
        try {
            $pdo = $query->connection->getPdo();
        } catch (\Throwable $e) {
            // ignore error for non-pdo laravel drivers
        }
        $bindings = $query->connection->prepareBindings($query->bindings);

        // Run EXPLAIN on this query (if needed)
        if (!$limited && $this->explainQuery && $pdo && preg_match('/^\s*(' . implode('|', $this->explainTypes) . ') /i', $sql)) {
            $statement = $pdo->prepare('EXPLAIN ' . $sql);
            $statement->execute($bindings);
            $explainResults = $statement->fetchAll(\PDO::FETCH_CLASS);
        }

        $bindings = $this->getDataFormatter()->checkBindings($bindings);
        if (!empty($bindings) && $this->renderSqlWithParams) {
            foreach ($bindings as $key => $binding) {
                // This regex matches placeholders only, not the question marks,
                // nested in quotes, while we iterate through the bindings
                // and substitute placeholders by suitable values.
                $regex = is_numeric($key)
                    ? "/(?<!\?)\?(?=(?:[^'\\\']*'[^'\\']*')*[^'\\\']*$)(?!\?)/"
                    : "/:{$key}(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/";

                // Mimic bindValue and only quote non-integer and non-float data types
                if (!is_int($binding) && !is_float($binding)) {
                    if ($pdo) {
                        try {
                            $binding = $pdo->quote((string) $binding);
                        } catch (\Exception $e) {
                            $binding = $this->emulateQuote($binding);
                        }
                    } else {
                        $binding = $this->emulateQuote($binding);
                    }
                }

                $sql = preg_replace($regex, addcslashes($binding, '$'), $sql, 1);
            }
        }

        $source = [];

        if (!$limited && $this->findSource) {
            try {
                $source = $this->findSource();
            } catch (\Exception $e) {
            }
        }

        $this->queries[] = [
            'query' => $sql,
            'type' => 'query',
            'bindings' => !$limited ? $this->getDataFormatter()->escapeBindings($bindings) : null,
            'start' => $startTime,
            'time' => $time,
            'memory' => $this->lastMemoryUsage ? memory_get_usage(false) - $this->lastMemoryUsage : 0,
            'source' => $source,
            'explain' => $explainResults,
            'connection' => $query->connection->getDatabaseName(),
            'driver' => $query->connection->getConfig('driver'),
            'hints' => ($this->showHints && !$limited) ? $hints : null,
            'show_copy' => $this->showCopyButton,
        ];

        if ($this->timeCollector !== null) {
            $this->timeCollector->addMeasure(Str::limit($sql, 100), $startTime, $endTime, [], 'db');
        }
    }

    /**
     * Mimic mysql_real_escape_string
     *
     * @param string $value
     * @return string
     */
    protected function emulateQuote($value)
    {
        $search = ["\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a"];
        $replace = ["\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z"];

        return "'" . str_replace($search, $replace, (string) $value) . "'";
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
     * @return string[]
     */
    protected function performQueryAnalysis($query)
    {
        // @codingStandardsIgnoreStart
        $hints = [];
        if (preg_match('/^\\s*SELECT\\s*`?[a-zA-Z0-9]*`?\\.?\\*/i', $query)) {
            $hints[] = 'Use <code>SELECT *</code> only if you need all columns from table';
        }
        if (preg_match('/ORDER BY RAND()/i', $query)) {
            $hints[] = '<code>ORDER BY RAND()</code> is slow, try to avoid if you can.
                You can <a href="https://stackoverflow.com/questions/2663710/how-does-mysqls-order-by-rand-work" target="_blank">read this</a>
                or <a href="https://stackoverflow.com/questions/1244555/how-can-i-optimize-mysqls-order-by-rand-function" target="_blank">this</a>';
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
            $hints[] = 'An argument has a leading wildcard character: <code>' . $matches[1] . '</code>.
                The predicate with this argument is not sargable and cannot use an index if one exists.';
        }
        return $hints;

        // @codingStandardsIgnoreEnd
    }

    /**
     * Use a backtrace to search for the origins of the query.
     *
     * @return array
     */
    protected function findSource()
    {
        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT, app('config')->get('debugbar.debug_backtrace_limit', 50));

        $sources = [];

        foreach ($stack as $index => $trace) {
            $sources[] = $this->parseTrace($index, $trace);
        }

        return array_slice(array_filter($sources), 0, 5);
    }

    /**
     * Parse a trace element from the backtrace stack.
     *
     * @param  int    $index
     * @param  array  $trace
     * @return object|bool
     */
    protected function parseTrace($index, array $trace)
    {
        $frame = (object) [
            'index' => $index,
            'namespace' => null,
            'name' => null,
            'file' => null,
            'line' => isset($trace['line']) ? $trace['line'] : '?',
        ];

        if (isset($trace['function']) && $trace['function'] == 'substituteBindings') {
            $frame->name = 'Route binding';

            return $frame;
        }

        if (
            isset($trace['class']) &&
            isset($trace['file']) &&
            !$this->fileIsInExcludedPath($trace['file'])
        ) {
            $frame->file = $trace['file'];

            if (isset($trace['object']) && is_a($trace['object'], 'Twig_Template')) {
                list($frame->file, $frame->line) = $this->getTwigInfo($trace);
            } elseif (strpos($frame->file, storage_path()) !== false) {
                $hash = pathinfo($frame->file, PATHINFO_FILENAME);

                if ($frame->name = $this->findViewFromHash($hash)) {
                    $frame->file = $frame->name[1];
                    $frame->name = $frame->name[0];
                } else {
                    $frame->name = $hash;
                }

                $frame->namespace = 'view';

                return $frame;
            } elseif (strpos($frame->file, 'Middleware') !== false) {
                $frame->name = $this->findMiddlewareFromFile($frame->file);

                if ($frame->name) {
                    $frame->namespace = 'middleware';
                } else {
                    $frame->name = $this->normalizeFilePath($frame->file);
                }

                return $frame;
            }

            $frame->name = $this->normalizeFilePath($frame->file);

            return $frame;
        }


        return false;
    }

    /**
     * Check if the given file is to be excluded from analysis
     *
     * @param string $file
     * @return bool
     */
    protected function fileIsInExcludedPath($file)
    {
        $normalizedPath = str_replace('\\', '/', $file);

        foreach ($this->backtraceExcludePaths as $excludedPath) {
            if (strpos($normalizedPath, $excludedPath) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find the middleware alias from the file.
     *
     * @param  string $file
     * @return string|null
     */
    protected function findMiddlewareFromFile($file)
    {
        $filename = pathinfo($file, PATHINFO_FILENAME);

        foreach ($this->middleware as $alias => $class) {
            if (strpos($class, $filename) !== false) {
                return $alias;
            }
        }
    }

    /**
     * Find the template name from the hash.
     *
     * @param  string $hash
     * @return null|array
     */
    protected function findViewFromHash($hash)
    {
        $finder = app('view')->getFinder();

        if (isset($this->reflection['viewfinderViews'])) {
            $property = $this->reflection['viewfinderViews'];
        } else {
            $reflection = new \ReflectionClass($finder);
            $property = $reflection->getProperty('views');
            $property->setAccessible(true);
            $this->reflection['viewfinderViews'] = $property;
        }

        $xxh128Exists = in_array('xxh128', hash_algos());

        foreach ($property->getValue($finder) as $name => $path) {
            if (($xxh128Exists && hash('xxh128', 'v2' . $path) == $hash) || sha1('v2' . $path) == $hash) {
                return [$name, $path];
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
                    return [$file, $templateLine];
                }
            }
        }

        return [$file, -1];
    }

    /**
     * Collect a database transaction event.
     * @param  string $event
     * @param \Illuminate\Database\Connection $connection
     * @return array
     */
    public function collectTransactionEvent($event, $connection)
    {
        $source = [];

        if ($this->findSource) {
            try {
                $source = $this->findSource();
            } catch (\Exception $e) {
            }
        }

        $this->queries[] = [
            'query' => $event,
            'type' => 'transaction',
            'bindings' => [],
            'start' => microtime(true),
            'time' => 0,
            'memory' => 0,
            'source' => $source,
            'explain' => [],
            'connection' => $connection->getDatabaseName(),
            'driver' => $connection->getConfig('driver'),
            'hints' => null,
            'show_copy' => false,
        ];
    }

    /**
     * Reset the queries.
     */
    public function reset()
    {
        $this->queries = [];
        $this->queryCount = 0;
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $totalTime = 0;
        $totalMemory = 0;
        $queries = $this->queries;

        $statements = [];
        foreach ($queries as $query) {
            $source = reset($query['source']);
            $totalTime += $query['time'];
            $totalMemory += $query['memory'];

            $statements[] = [
                'sql' => $this->getDataFormatter()->formatSql($query['query']),
                'type' => $query['type'],
                'params' => [],
                'bindings' => $query['bindings'],
                'hints' => $query['hints'],
                'show_copy' => $query['show_copy'],
                'backtrace' => array_values($query['source']),
                'start' => $query['start'] ?? null,
                'duration' => $query['time'],
                'duration_str' => ($query['type'] == 'transaction') ? '' : $this->formatDuration($query['time']),
                'memory' => $query['memory'],
                'memory_str' => $query['memory'] ? $this->getDataFormatter()->formatBytes($query['memory']) : null,
                'filename' => $this->getDataFormatter()->formatSource($source, true),
                'source' => $this->getDataFormatter()->formatSource($source),
                'xdebug_link' => is_object($source) ? $this->getXdebugLink($source->file ?: '', $source->line) : null,
                'connection' => $query['connection'],
            ];

            // Add the results from the explain as new rows
            if ($query['driver'] === 'pgsql') {
                $explainer = trim(implode("\n", array_map(function ($explain) {
                    return $explain->{'QUERY PLAN'};
                }, $query['explain'])));

                if ($explainer) {
                    $statements[] = [
                        'sql' => " - EXPLAIN: {$explainer}",
                        'type' => 'explain',
                    ];
                }
            } elseif ($query['driver'] === 'sqlite') {
                $vmi  = '<table style="margin:-5px -11px !important;width: 100% !important">';
                $vmi .= "<thead><tr>
                    <td>Address</td>
                    <td>Opcode</td>
                    <td>P1</td>
                    <td>P2</td>
                    <td>P3</td>
                    <td>P4</td>
                    <td>P5</td>
                    <td>Comment</td>
                    </tr></thead>";

                foreach ($query['explain'] as $explain) {
                    $vmi .= "<tr>
                        <td>{$explain->addr}</td>
                        <td>{$explain->opcode}</td>
                        <td>{$explain->p1}</td>
                        <td>{$explain->p2}</td>
                        <td>{$explain->p3}</td>
                        <td>{$explain->p4}</td>
                        <td>{$explain->p5}</td>
                        <td>{$explain->comment}</td>
                        </tr>";
                }

                $vmi .= '</table>';

                $statements[] = [
                    'sql' => " - EXPLAIN:",
                    'type' => 'explain',
                    'params' => [
                        'Virtual Machine Instructions' => $vmi,
                    ]
                ];
            } else {
                foreach ($query['explain'] as $explain) {
                    $statements[] = [
                        'sql' => " - EXPLAIN # {$explain->id}: `{$explain->table}` ({$explain->select_type})",
                        'type' => 'explain',
                        'params' => $explain,
                        'row_count' => $explain->rows,
                        'stmt_id' => $explain->id,
                    ];
                }
            }
        }

        if ($this->durationBackground) {
            if ($totalTime > 0) {
                // For showing background measure on Queries tab
                $start_percent = 0;

                foreach ($statements as $i => $statement) {
                    if (!isset($statement['duration'])) {
                        continue;
                    }

                    $width_percent = $statement['duration'] / $totalTime * 100;

                    $statements[$i] = array_merge($statement, [
                        'start_percent' => round($start_percent, 3),
                        'width_percent' => round($width_percent, 3),
                    ]);

                    $start_percent += $width_percent;
                }
            }
        }

        if ($this->softLimit && $this->hardLimit && ($this->queryCount > $this->softLimit && $this->queryCount > $this->hardLimit)) {
            array_unshift($statements, [
                'sql' => '# Query soft and hard limit for Debugbar are reached. Only the first ' . $this->softLimit . ' queries show details. Queries after the first ' . $this->hardLimit .  ' are ignored. Limits can be raised in the config (debugbar.options.db.soft/hard_limit).',
                'type' => 'info',
            ]);
            $statements[] = [
                'sql' => '... ' . ($this->queryCount - $this->hardLimit) . ' additional queries are executed but now shown because of Debugbar query limits. Limits can be raised in the config (debugbar.options.db.soft/hard_limit)',
                'type' => 'info',
            ];
        } elseif ($this->hardLimit && $this->queryCount > $this->hardLimit) {
            array_unshift($statements, [
                'sql' => '# Query hard limit for Debugbar is reached after ' . $this->hardLimit . ' queries, additional ' . ($this->queryCount - $this->hardLimit) . ' queries are not shown.. Limits can be raised in the config (debugbar.options.db.hard_limit)',
                'type' => 'info',
            ]);
            $statements[] = [
                'sql' => '... ' . ($this->queryCount - $this->hardLimit) . ' additional queries are executed but now shown because of Debugbar query limits. Limits can be raised in the config (debugbar.options.db.hard_limit)',
                'type' => 'info',
            ];
        } elseif ($this->softLimit && $this->queryCount > $this->softLimit) {
            array_unshift($statements, [
                'sql' => '# Query soft limit for Debugbar is reached after ' . $this->softLimit . ' queries, additional ' . ($this->queryCount - $this->softLimit) . ' queries only show the query. Limit can be raised in the config. Limits can be raised in the config (debugbar.options.db.soft_limit)',
                'type' => 'info',
            ]);
        }

        $data = [
            'nb_statements' => $this->queryCount,
            'nb_failed_statements' => 0,
            'accumulated_duration' => $totalTime,
            'accumulated_duration_str' => $this->formatDuration($totalTime),
            'memory_usage' => $totalMemory,
            'memory_usage_str' => $totalMemory ? $this->getDataFormatter()->formatBytes($totalMemory) : null,
            'statements' => $statements
        ];
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
        return [
            "queries" => [
                "icon" => "database",
                "widget" => "PhpDebugBar.Widgets.SQLQueriesWidget",
                "map" => "queries",
                "default" => "[]"
            ],
            "queries:badge" => [
                "map" => "queries.nb_statements",
                "default" => 0
            ]
        ];
    }
}
