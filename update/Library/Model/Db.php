<?php
/**
 * @package ImpressPages

 *
 */

namespace IpUpdate\Library\Model;


class Db
{
    const DRIVER_PDO_MYSQL = 0; 
    const DRIVER_MYSQL = 1;
    

    /**
     * @param $cf configuration parsed using configuration parser
     * @param int $driver
     * @return \PDO int
     * @throws \Exception
     */
    public function connect($cf, $driver = self::DRIVER_PDO_MYSQL)
    {
        $config = \Ip\Config::getRaw('db');
        switch($driver) {
            case self::DRIVER_PDO_MYSQL:
                try {
                    $pdo = new \PDO('mysql:host='.str_replace(':', ';port=', $config['hostname']).';dbname='.$config['database'], $config['username'], $config['password']);
                    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    return $pdo;
                } catch (PDOException $e) {
                    throw new \Exception($e->getMessage());
                }
            break;
            case self::DRIVER_MYSQL:
                $connection = mysql_connect($config['hostname'], $config['username'], $config['password']);
                if ($connection) {
                    mysql_select_db($config['database']);
                    mysql_query("SET CHARACTER SET ".$config['charset']);
                    return $connection;
                } else {
                    throw new \Exception("Can\'t connect to database.");
                }
            break;
            default:
                throw new \Exception("Unknown driver");
            break;
        }

    }

}