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
        return \Ip\Db::getConnection();
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
        \Ip\Module\Install\Model::createAndUseDatabase($dbName);
        $this->dbName = $dbName;

    }


    private function dropDatabase()
    {
        \Ip\Db::execute('DROP DATABASE `' . $this->dbname . '`');
        \Ip\Db::disconnect();

        $this->dbName = null;
        $this->connection = null;
    }

}