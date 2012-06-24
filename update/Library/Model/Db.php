<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Library\Model;


class Db
{
    private static $dbh;
    /**
     *
     * @param array $configuration configuration parsed using configuration parser
     */
    public function connect($cf)
    {
        if (!self::$dbh) {
            try {
                self::$dbh = new \PDO('mysql:host='.$cf['DB_SERVER'].';dbname='.$cf['DB_DATABASE'], $cf['DB_USERNAME'], $cf['DB_PASSWORD']);
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
        return self::$dbh;
    }

    public function disconnect()
    {
        self::$dbh = null;
    }
}