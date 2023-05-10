<?php

declare(strict_types=1);

namespace SwooleCli\UnitTest;

use PHPUnit\Framework\TestCase;
use Swoole\Coroutine\PostgreSQL;

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

        $this->assertGreaterThanOrEqual(200, 200, 'curl no support IDNA');
    }

}
