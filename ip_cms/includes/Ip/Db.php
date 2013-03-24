<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Ip;


if (!defined('CMS')) exit;

/**
 *
 * Create and supply services to modules
 *
 */
class Db
{
    const TYPE_PDO = 1;
    const TYPE_MYSQL = 2;

    private static $pdoConnection;


    /**
     * 
     * @param int $type (eg. \Ip\Db::TYPE_PDO, \Ip\Db::TYPE_MYSQL)
     * @throws \Ip\CoreException
     * @return \PDO
     */
    public static function getConnection($type = self::TYPE_PDO)
    {
        switch ($type) {
            case self::TYPE_MYSQL:
                return \Db::getConnection();
                break;
            case self::TYPE_PDO:
                if (!self::$pdoConnection) {
                    try {
                        self::$pdoConnection = new \PDO('mysql:host='.DB_SERVER.';dbname='.DB_DATABASE, DB_USERNAME, DB_PASSWORD);
                        self::$pdoConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
                        self::$pdoConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                        $dt = new \DateTime();
                        $offset = $dt->format("P");
                        self::$pdoConnection->exec("SET time_zone='$offset';");
                    } catch (\PDOException $e) {
                        throw new \Ip\CoreException("Can't connect to database", \Ip\CoreException::DB, $e);
                    }
                }
                return self::$pdoConnection;
                break;
        }
    }

}