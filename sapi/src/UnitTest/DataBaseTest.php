<?php

declare(strict_types=1);

namespace SwooleCli\UnitTest;

use PHPUnit\Framework\TestCase;
use Swoole\Coroutine\PostgreSQL;
use Swoole\Coroutine\MySQL;
use Swoole\Coroutine\Redis;

use function Swoole\Coroutine\run;

class DataBaseTest extends TestCase
{
    public function testSwoolePGSQL(): void
    {
        run(function () {
            $pg = new PostgreSQL();
            $conn = $pg->connect("host=192.168.3.26 port=5432 dbname=postgres user=postgres password=example");
            if (!$conn) {
                var_dump($pg->error);
                return;
            }

            $stmt = $pg->query("select * from pg_tables");
            $arr = $stmt->fetchAll();


            $this->assertTrue(!empty($arr), 'pgsql query errro');
            $stmt = $pg->query("create database test");
            $conn = $pg->connect("host=192.168.3.26 port=5432 dbname=test user=postgres password=example");
            $stmt = $pg->query("select * from pg_tables");
            $arr = $stmt->fetchAll();
            var_dump($arr);
        });
    }

    public function testSwooleMysql()
    {
        run(function () {
            $swoole_mysql = new MySQL();
            $swoole_mysql->connect([
                'host'     => '127.0.0.1',
                'port'     => 3306,
                'user'     => 'user',
                'password' => 'pass',
                'database' => 'test',
            ]);
            $res = $swoole_mysql->query('select sleep(1)');
            var_dump($res);
        });
    }

    public function testSwooleRedis()
    {
        run(function () {
            $redis = new Redis();
            $redis->connect('127.0.0.1', 6379);
            $val = $redis->get('key');
        });
    }

}
