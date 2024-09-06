<?php

namespace Barryvdh\Debugbar\Support;

use DB;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class Explain
{
    public function confirm(string $connection): ?string
    {
        return match (DB::connection($connection)->getDriverName()) {
            'mysql' => 'The query and EXPLAIN output is sent to mysqlexplain.com. Do you want to continue?',
            'pgsql' => 'The query and EXPLAIN output is sent to explain.dalibo.com. Do you want to continue?',
            default => null,
        };
    }

    public function pack(string $connection, string $sql, ?array $bindings, int $validUntilTimestamp): ?string
    {
        return match (DB::connection($connection)->getDriverName()) {
            'mysql', 'pgsql' => Crypt::encrypt(compact('connection', 'sql', 'bindings', 'validUntilTimestamp')),
            default => null,
        };
    }

    private function unpack(string $encrypted): array
    {
        try {
            $data = Crypt::decrypt($encrypted);
        } catch (DecryptException $e) {
            throw new Exception('Query to execute could not be verified.', previous: $e);
        }

        if ($data['validUntilTimestamp'] < time()) {
            throw new Exception('Allowed time to analyze query has expired. Please reload the page.');
        }

        return Arr::except($data, ['validUntilTimestamp']);
    }

    public function generateRawExplain(string $encrypted): array
    {
        $data = $this->unpack($encrypted);
        $connection = DB::connection($data['connection']);

        return match ($driver = $connection->getDriverName()) {
            'mysql' => $connection->select("EXPLAIN {$data['sql']}", $data['bindings']),
            'pgsql' => array_column($connection->select("EXPLAIN {$data['sql']}", $data['bindings']), 'QUERY PLAN'),
            default => throw new Exception("Visual explain not available for driver '{$driver}'."),
        };
    }

    public function generateVisualExplain(string $encrypted): string
    {
        $data = $this->unpack($encrypted);
        $connection = DB::connection($data['connection']);

        return match ($driver = $connection->getDriverName()) {
            'mysql' => $this->generateVisualExplainMysql($connection, $data['sql'], $data['bindings']),
            'pgsql' => $this->generateVisualExplainPgsql($connection, $data['sql'], $data['bindings']),
            default => throw new Exception("Visual explain not available for driver '{$driver}'."),
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
            'explain_tree' => rescue(fn () => $connection->selectOne("EXPLAIN FORMAT=TREE {$query}", $bindings), report: false)->EXPLAIN,
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
