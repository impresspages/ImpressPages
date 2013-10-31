<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace PhpUnit\Helper;

class TestDb
{
    private $connection;
    private $dbName;


    public function __construct()
    {
        $this->createDatabase('ip_test_'.date('Y-m-d-H-i-s'));
    }

    public function getPdoConnection()
    {
        return $this->connection;
    }


    public function getDbName()
    {
        return $this->dbName;
    }


    public function getDbHost()
    {
        return TEST_DB_HOST;
    }


    public function getDbUser()
    {
        return TEST_DB_USER;
    }

    public function getDbPass()
    {
        return TEST_DB_PASS;
    }


    public function __destruct()
    {
        if ($this->dbName) {
            $this->dropDatabase($this->dbName);
        }
    }

    /**
     * @param string $dbName
     * @return \PDO
     * @throws \Exception
     */
    private function createDatabase($dbName)
    {
        $connection = mysql_connect(TEST_DB_HOST, TEST_DB_USER, TEST_DB_PASS);
        if(!$connection) {
            throw new \Exception('Can\'t connect to database.');
        }

        $sql = "CREATE DATABASE `".$dbName."` CHARACTER SET utf8";
        $rs = mysql_query($sql, $connection);
        if (!$rs) {
            throw new \Exception("Can't create database. ".$sql);
        }

        $sql = "ALTER DATABASE `".$dbName."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
        $rs = mysql_query($sql, $connection);
        if (!$rs) {
            throw new \Exception("Can't create database. ".$sql);
        }
        mysql_close($connection);

        $this->dbName = $dbName;
        $this->connnection = new \PDO('mysql:host='.TEST_DB_HOST.';dbname='.$this->dbName, TEST_DB_USER, TEST_DB_PASS);

    }


    private function dropDatabase()
    {
        $connection = mysql_connect(TEST_DB_HOST, TEST_DB_USER, TEST_DB_PASS);
        if(!$connection) {
            throw new \Exception('Can\'t connect to database.');
        }

        mysql_query("DROP DATABASE `".$this->dbName."`", $connection);

        mysql_close($connection);

        $this->dbName = null;
        $this->connection = null;
    }

}