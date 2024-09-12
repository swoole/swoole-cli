<?php

declare(strict_types=1);

namespace SwooleCli\UnitTest;

use PHPUnit\Framework\TestCase;
use Swoole\Runtime;
use function Swoole\Coroutine\run;

final class SwoolePGSQLTestV6 extends TestCase
{
    private $pg = null;
    private $pg_master = null;

    public function testSwoolePGSQL(): void
    {
        $oriErrorLevel = error_reporting(E_ALL);
        $oriFlags = Runtime::getHookFlags();
        Runtime::setHookFlags(SWOOLE_HOOK_PDO_PGSQL);
        run(function () {
            $this->createDataBase();
            $this->createTable();
            $this->insertTableData();
            $this->selectTableData();
            $this->deleteTableData();
            $this->dropTable();
            $this->pg = null;
            $this->dropDatabase();
            $this->pg_master = null;
        });
        Runtime::setHookFlags($oriFlags);
        error_reporting($oriErrorLevel);
    }

    protected function createDataBase()
    {
        $dbh = new \PDO("pgsql:dbname=postgres;host=127.0.0.1;port=5432", "postgres", "example");
        $this->assertEquals(NULL, $dbh->errorCode(), 'pgsql connection postgres  Error ,Error Info : ' . $dbh->errorInfo()[2]);

        $this->pg_master = $dbh;
        $res = $dbh->query("SELECT *  FROM pg_database WHERE datname = 'user_center'", \PDO::FETCH_ASSOC);
        $arr = $res->fetchAll();
        if (!empty($arr)) {
            $this->dropDatabase();
        }


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
        echo $sql . PHP_EOL;
        $res = $dbh->exec($sql);
        $this->assertEquals(0, $res, 'create database user_center  Error ,Error Info : ' . $dbh->errorInfo()[2]);
    }

    protected function createTable()
    {
        $dbh = new \PDO("pgsql:dbname=user_center;host=127.0.0.1;port=5432", "postgres", "example");
        $this->assertEquals(NULL, $dbh->errorCode(), 'connection database user_center  Error ,Error Info : ' . $dbh->errorInfo()[2]);

        $this->pg = $dbh;
        $sql = "select *  from pg_tables where schemaname = 'public' and tablename='users'";
        $stmt = $this->pg->query($sql, \PDO::FETCH_ASSOC);
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
            echo $table . PHP_EOL;
            $res = $this->pg->exec($table);
            $this->assertEquals(0, $res, 'create table users  Error ,Error Info : ' . $dbh->errorInfo()[2]);
        }
    }

    protected function insertTableData()
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
	VALUES (:username, :nickname, :password, :salt, :avatar, :email, :mobile,  :updated_at, :login_channel);

EOF;


        echo $sql . PHP_EOL;

        $stmt = $this->pg->prepare($sql);
        // :username, :nickname, :password, :salt, :avatar, :email, :mobile,  :updated_at, :login_channel
        $stmt->bindValue(':username', "username", \PDO::PARAM_STR);
        $stmt->bindValue(':nickname', "example", \PDO::PARAM_STR);
        $stmt->bindValue(':password', $password, \PDO::PARAM_STR);
        $stmt->bindValue(':salt', $salt, \PDO::PARAM_STR);
        $stmt->bindValue(':avatar', "https://wenda.swoole.com/dist/skin1/images/logo.png", \PDO::PARAM_STR);
        $stmt->bindValue(':email', "example2@qq.com", \PDO::PARAM_STR);
        $stmt->bindValue(':mobile', "861888888888", \PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', $time, \PDO::PARAM_STR);
        $stmt->bindValue(':login_channel', 1, \PDO::PARAM_INT);
        $res = $stmt->execute();

        $this->assertTrue($res, 'insert data  Error ,Error Info : ' . $this->pg->errorInfo()[2]);
    }

    protected function selectTableData()
    {
        $sql = <<<EOF
SELECT * FROM public.users
ORDER BY id ASC

EOF;

        echo $sql . PHP_EOL;

        $stmt = $this->pg->query($sql);
        $list = $stmt->fetchAll();
        $this->assertGreaterThan(0, count($list), 'select data   Error ,Error Info : ' . $stmt->errorInfo()[2]);
    }

    protected function deleteTableData()
    {
        $sql = <<<'EOF'
DELETE FROM users
WHERE username=:username

EOF;

        echo $sql . PHP_EOL;

        $stmt = $this->pg->prepare($sql);
        $stmt->bindValue(":username", "username", \PDO::PARAM_STR);

        $stmt->execute();

        $this->assertGreaterThan(0, $stmt->rowCount(), 'delete data   Error ,Error Info : ' . $this->pg->errorInfo()[2]);
    }

    protected function dropTable()
    {
        $sql = <<<'EOF'

DROP TABLE  IF EXISTS  users ;
DROP SEQUENCE  IF EXISTS users_id_seq ;
EOF;

        echo $sql . PHP_EOL;
        $res = $this->pg->exec($sql);
        $this->assertEquals(0, $res, 'drop table users   Error ,Error Info : ' . $this->pg->errorInfo()[2]);
    }

    protected function dropDatabase()
    {
        $sql = <<<'EOF'

DROP DATABASE IF EXISTS user_center
EOF;

        echo $sql . PHP_EOL;
        $this->pg_master->exec($sql);
        $this->assertEquals("00000", $this->pg_master->errorCode(), 'drop database user_center   Error ,Error Info : ' . $this->pg_master->errorInfo()[2]);
    }
}
