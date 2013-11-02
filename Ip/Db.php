<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;

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
                        $config = \Ip\Config::getRaw('db');

                        $dsn = 'mysql:host='.str_replace(':', ';port=', $config['hostname']);
                        if (!empty($config['database'])) {
                            $dsn .= ';dbname='. $config['database'];
                        }

                        self::$pdoConnection = new \PDO($dsn, $config['username'], $config['password']);
                        self::$pdoConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
                        $dt = new \DateTime();
                        $offset = $dt->format("P");
                        self::$pdoConnection->exec("SET time_zone='$offset';");
                        self::$pdoConnection->exec("SET CHARACTER SET ". $config['charset']);
                    } catch (\PDOException $e) {
                        throw new \Ip\CoreException("Can't connect to database. Stack trace hidden for security reasons", \Ip\CoreException::DB);
                        //PHP traces all details of error including DB password. This could be a disaster on live server. So we hide that data.
                    }
                }
                return self::$pdoConnection;
                break;
        }
    }

}