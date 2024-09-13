<?php

namespace Barryvdh\Debugbar\Support;

use Exception;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Explain
{
    public function isVisualExplainSupported(string $connection): bool
    {
        $driver = DB::connection($connection)->getDriverName();
        if ($driver === 'pgsql') {
            return true;
        }
        if ($driver === 'mysql') {
            // Laravel 11 added a new MariaDB database driver but older Laravel versions handle MySQL and MariaDB with
            // the same driver - and even with new versions you can use the MySQL driver while connection to a MariaDB
            // database. This query uses a feature implemented only in MariaDB to differentiate them.
            try {
                DB::connection($connection)->select('SELECT * FROM seq_1_to_1');

                return false;
            } catch (QueryException) {
                // This exception is expected when using MySQL as sequence tables are only available with MariaDB. So
                // the exception gets silenced as the check for MySQL has succeeded.
                return true;
            }
        }

        return false;
    }

    public function confirmVisualExplain(string $connection): ?string
    {
        return match (DB::connection($connection)->getDriverName()) {
            'mysql' => 'The query and EXPLAIN output is sent to mysqlexplain.com. Do you want to continue?',
            'pgsql' => 'The query and EXPLAIN output is sent to explain.dalibo.com. Do you want to continue?',
            default => null,
        };
    }

    public function hash(string $connection, string $sql, array $bindings): string
    {
        $bindings = json_encode($bindings);

        return match (DB::connection($connection)->getDriverName()) {
            'mariadb', 'mysql', 'pgsql' => hash_hmac('sha256', "{$connection}::{$sql}::{$bindings}", config('app.key')),
            default => null,
        };
    }

    private function verify(string $connection, string $sql, array $bindings, string $hash): void
    {
        if (!hash_equals($this->hash($connection, $sql, $bindings), $hash)) {
            throw new Exception('Query to execute could not be verified.');
        }
    }

    public function generateRawExplain(string $connection, string $sql, array $bindings, string $hash): array
    {
        $this->verify($connection, $sql, $bindings, $hash);

        $connection = DB::connection($connection);

        return match ($driver = $connection->getDriverName()) {
            'mariadb', 'mysql' => $connection->select("EXPLAIN {$sql}", $bindings),
            'pgsql' => array_column($connection->select("EXPLAIN {$sql}", $bindings), 'QUERY PLAN'),
            default => throw new Exception("Visual explain not available for driver '{$driver}'."),
        };
    }

    public function generateVisualExplain(string $connection, string $sql, array $bindings, string $hash): string
    {
        $this->verify($connection, $sql, $bindings, $hash);
        if (!$this->isVisualExplainSupported($connection)) {
            throw new Exception('Visual explain not available for this connection.');
        }

        $connection = DB::connection($connection);

        return match ($connection->getDriverName()) {
            'mysql' => $this->generateVisualExplainMysql($connection, $sql, $bindings),
            'pgsql' => $this->generateVisualExplainPgsql($connection, $sql, $bindings),
        };
    }

    private function generateVisualExplainMysql(ConnectionInterface $connection, string $query, array $bindings): string
    {
        return Http::withHeaders([
            'User-Agent' => 'barryvdh/laravel-debugbar',
        ])->post('https://api.mysqlexplain.com/v2/explains', [
            'query' => $query,
            'bindings' => $bindings,
            'version' => $connection->selectOne("SELECT VERSION()")->{'VERSION()'},
            'explain_json' => $connection->selectOne("EXPLAIN FORMAT=JSON {$query}", $bindings)->EXPLAIN,
            'explain_tree' => rescue(fn () => $connection->selectOne("EXPLAIN FORMAT=TREE {$query}", $bindings)->EXPLAIN, report: false),
        ])->throw()->json('url');
    }

    private function generateVisualExplainPgsql(ConnectionInterface $connection, string $query, array $bindings): string
    {
        return (string) Http::asForm()->post('https://explain.dalibo.com/new', [
            'query' => $query,
            'plan' => $connection->selectOne("EXPLAIN (FORMAT JSON) {$query}", $bindings)->{'QUERY PLAN'},
            'title' => '',
        ])->effectiveUri();
    }
}
