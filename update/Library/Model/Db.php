<?php
/**
 * @package ImpressPages

 *
 */

namespace IpUpdate\Library\Model;


class Db
{
    /**
     * @param $cf configuration parsed using configuration parser
     * @return \PDO int
     * @throws \Exception
     */
    public function connect($cf)
    {
        $config = $cf['db'];
        try {
            $pdo = new \PDO('mysql:host='.str_replace(':', ';port=', $config['hostname']).';dbname='.$config['database'], $config['username'], $config['password']);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            throw new \IpUpdate\Library\UpdateException($e->getMessage(), \IpUpdate\Library\UpdateException::SQL);
        }
    }

}