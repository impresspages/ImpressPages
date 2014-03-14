<?php

namespace PhpUnit;

use PhpUnit\Helper\TestEnvironment;

class GeneralTestCase extends \PHPUnit_Extensions_Database_TestCase
{

    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    /**
     * @return \PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new \PDO('mysql:host='.TEST_DB_HOST.';dbname='.TEST_DB_NAME, TEST_DB_USER, TEST_DB_PASS);
                self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
                $dt = new \DateTime();
                $offset = $dt->format("P");
                self::$pdo->exec("SET time_zone='$offset';");
                self::$pdo->exec("SET CHARACTER SET utf8");
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo);
        }

        return $this->conn;
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createXMLDataSet(TEST_FIXTURE_DIR.'empty.xml');
    }

    protected function setup()
    {
        \PhpUnit\Helper\TestEnvironment::setup();

        parent::setup();
    }

}