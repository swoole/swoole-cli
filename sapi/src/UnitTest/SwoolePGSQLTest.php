<?php

declare(strict_types=1);

namespace SwooleCli\UnitTest;

use PHPUnit\Framework\TestCase;
use Swoole\Coroutine\PostgreSQL;

use function Swoole\Coroutine\run;

final class SwoolePGSQLTest extends TestCase
{
    private $pg = null;
    private $pg_master = null;

    public function testSwoolePGSQL(): void
    {
        run(function () {
            $this->createDataBase();
            $this->createTable();
            $this->insertTableData();
            $this->selectTableData();
            $this->deleteTableData();
            $this->dropTable();
            $this->dropDatabase();
        });
    }


    public function createDataBase()
    {

        $pg = new PostgreSQL();
        $conn = $pg->connect("host=127.0.0.1 port=5432 dbname=postgres user=postgres password=example");
        if (!$conn) {
            $this->assertNotTrue($conn, 'pgsql connection postgres error,error info :' . $pg->error);
            return false;
        }
        $this->pg_master = $pg;
        $stmt = $pg->query("SELECT *  FROM pg_database WHERE datname = 'user_center'");
        $arr = $stmt->fetchAssoc();
        if (empty($arr)) {
            $sql = <<<EOF
CREATE DATABASE user_center
    WITH
    OWNER = postgres
    ENCODING = 'UTF8'
    LC_COLLATE = 'en_US.utf8'
    LC_CTYPE = 'en_US.utf8'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1
    IS_TEMPLATE = False
EOF;

            $pg->query($sql);
            $this->assertEquals(0, $pg->errCode, 'create database user_center  sucess' . $pg->error);
        }

    }

    public function createTable()
    {
        $pg = new PostgreSQL();

        $conn = $pg->connect("host=127.0.0.1 port=5432 dbname=user_center user=postgres password=example");
        if (!$conn) {
            $this->assertNotTrue($conn, 'erro_info' . $pg->error);
            return;
        }
        $this->pg = $pg;
        $sql = "select *  from pg_tables where schemaname = 'public' and tablename='users'";
        $stmt = $pg->query($sql);
        $res = $stmt->fetchAll();
        if (empty($res)) {
            # USER  是PGSQL 关键字，不能用作表名
            $table = <<<'EOF'
CREATE TABLE  users (
  id bigint  NOT NULL ,
  username varchar(32) NOT NULL ,
  nickname varchar(40) NOT NULL,
  password varchar(255) NOT NULL ,
  salt varchar(255) NOT NULL,
  avatar varchar(255) DEFAULT '' ,
  email varchar(100) DEFAULT NULL ,
  mobile varchar(16) DEFAULT NULL ,
  created_at TIMESTAMPTZ DEFAULT  current_timestamp ,
  updated_at TIMESTAMPTZ DEFAULT NULL ,
  login_at TIMESTAMPTZ DEFAULT NULL ,
  status SMALLINT DEFAULT 0 ,
  login_channel SMALLINT	 DEFAULT NULL ,
  PRIMARY KEY (id)
) ;

CREATE SEQUENCE users_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;

alter table users alter column id set default nextval('users_id_seq');

EOF;
            $pg->query($table);
            $this->assertEquals(0, $pg->errCode, 'create table users sucess' . $pg->error);
        }
    }

    public function insertTableData()
    {
        $password = 'example';
        $salt = bin2hex(openssl_random_pseudo_bytes(rand(4, 20)));
        $password = hash('sha256', $password, false) . $salt;
        # ISO8601
        $time = date('c', time());
        $time = date('Y-m-d\TH:i:s.Z\Z', time());

        $sql = <<<EOF
INSERT INTO public.users(
	username, nickname, password, salt, avatar, email, mobile,  updated_at, login_channel)
	VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9);

EOF;
        $list = [
            [
                "username1",
                "example1",
                "{$password}",
                "{$salt}",
                "https://wenda.swoole.com/dist/skin1/images/logo.png",
                "example1@qq.com",
                "861888888888",
                "{$time}",
                "1"
            ],
            [
                "username2",
                "example2",
                "{$password}",
                "{$salt}",
                "https://wenda.swoole.com/dist/skin1/images/logo.png",
                "example2@qq.com",
                "861888888888",
                "{$time}",
                "1"
            ]
        ];

        echo $sql;

        $stmt = $this->pg->prepare($sql);

        $i = 100;
        while ($i >= 1) {
            foreach ($list as $data) {
                $res = $stmt->execute($data);
            }
            $i--;
        }
        $this->assertGreaterThanOrEqual(1, $stmt->affectedRows(), 'insert data sucess' . $this->pg->error);
        var_dump($stmt->affectedRows());
    }

    public function selectTableData()
    {
        $sql = <<<EOF
SELECT * FROM public.users
ORDER BY id ASC

EOF;

        echo $sql;

        $stmt = $this->pg->query($sql);
        $list = $stmt->fetchAll();
        $this->assertGreaterThan(1, count($list), 'select data' . $this->pg->error);
    }

    public function deleteTableData()
    {
        $sql = <<<'EOF'
DELETE FROM users
WHERE username=$1

EOF;

        echo $sql;

        $stmt = $this->pg->prepare($sql);

        $stmt->execute(['username2']);

        $this->assertGreaterThan(10, $stmt->affectedRows(), 'delete data' . $this->pg->error);
    }

    public function dropTable()
    {
        $sql = <<<'EOF'

DROP TABLE users ;
DROP SEQUENCE users_id_seq ;
EOF;

        echo $sql;

        $this->pg->query($sql);
        $this->assertEquals(0, $this->pg->errCode, 'drop table users' . $this->pg->error);
    }

    public function dropDatabase()
    {
        $sql = <<<'EOF'

DROP DATABASE IF EXISTS user_center
EOF;

        echo $sql;

        $this->pg_master->query($sql);
        echo PHP_EOL . $this->pg->error . PHP_EOL;
        $this->assertEquals(0, $this->pg_master->errCode, 'drop database user_center' . $this->pg_master->error);
    }
}
