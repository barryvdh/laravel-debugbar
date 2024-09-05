<?php

namespace Barryvdh\Debugbar\Support;

use DB;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class VisualExplain
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

    public function generateUrl(string $encrypted): string
    {
        try {
            $data = Crypt::decrypt($encrypted);
        } catch (DecryptException $e) {
            throw new Exception('Query to execute could not be verified.', previous: $e);
        }

        if ($data['validUntilTimestamp'] < time()) {
            throw new Exception('Allowed time to analyze query has expired. Please reload the page.');
        }


        return match ($driver = DB::connection($data['connection'])->getDriverName()) {
            'mysql' => $this->generateUrlMysql($data),
            'pgsql' => $this->generateUrlPgsql($data),
            default => throw new Exception("Visual explain not available for driver '{$driver}'."),
        };
    }

    private function generateUrlMysql(array $data): string
    {
        return Http::withHeaders([
            'User-Agent' => 'barryvdh/laravel-debugbar',
        ])->post('https://api.mysqlexplain.com/v2/explains', [
            'query' => $data['sql'],
            'bindings' => $data['bindings'],
            'version' => DB::connection($data['connection'])->selectOne("SELECT VERSION()")->{'VERSION()'},
            'explain_json' => DB::connection($data['connection'])->selectOne("EXPLAIN FORMAT=JSON {$data['sql']}", $data['bindings'] ?? [])->EXPLAIN,
            'explain_tree' => rescue(fn () => DB::connection($data['connection'])->selectOne("EXPLAIN FORMAT=TREE {$data['sql']}", $data['bindings'] ?? []), report: false)->EXPLAIN,
        ])->throw()->json('url');
    }

    private function generateUrlPgsql(array $data): string
    {
        return (string) Http::asForm()->post('https://explain.dalibo.com/new', [
            'query' => $data['sql'],
            'plan' => DB::connection($data['connection'])->selectOne("EXPLAIN (FORMAT JSON) {$data['sql']}", $data['bindings'] ?? [])->{'QUERY PLAN'},
            'title' => '',
        ])->effectiveUri();
    }
}
