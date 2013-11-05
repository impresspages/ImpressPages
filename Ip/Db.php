<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip {

/**
 *
 * Create and supply services to modules
 *
 */
class Db
{
    const TYPE_PDO = 1;
    const TYPE_MYSQL = 2;

    /**
     * @var \PDO
     */
    private static $pdoConnection;

    private static $tablePrefix;

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
                return Deprecated\Db::getConnection();
                break;
            case self::TYPE_PDO:
                if (!self::$pdoConnection) {
                    try {
                        $config = \Ip\Config::getRaw('db');

                        if (empty($config)) {
                            throw new \Ip\CoreException("Can't connect to database. No connection config found or \\Ip\\Db::disconnect() has been used.", \Ip\CoreException::DB);
                        }

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

                    static::$tablePrefix = $config['tablePrefix'];
                    \Ip\Config::_setRaw('db', null);
                }
                return self::$pdoConnection;
                break;
        }
    }

    public static function disconnect()
    {
        \Ip\Config::_setRaw('db', null);
        self::$pdoConnection = null;
    }

    public static function fetchValue($sql, $params = array())
    {
        $query = static::getConnection()->prepare($sql . " LIMIT 1");
        foreach ($params as $key => $value) {
            $query->bindValue(is_numeric($key) ? $key + 1 : $key, $value);
        }

        $query->execute();
        // TODOX check if $query->fetchColumn() would do
        $result = $query->fetchAll(\PDO::FETCH_NUM);

        return $result ? $result[0][0] : null;
    }

    public static function fetchRow($sql, $params = array())
    {
        $query = static::getConnection()->prepare($sql . " LIMIT 1");
        foreach ($params as $key => $value) {
            $query->bindValue(is_numeric($key) ? $key + 1 : $key, $value);
        }

        $query->execute();
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);

        return $result ? $result[0] : null;
    }

    public static function fetchAll($sql, $params = array())
    {
        $query = static::getConnection()->prepare($sql);
        foreach ($params as $key => $value) {
            $query->bindValue(is_numeric($key) ? $key + 1 : $key, $value);
        }

        $query->execute();
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);

        return $result ? $result : array();
    }

    /**
     * @param string $sql
     * @param array $params
     * @return int the number of rows affected by the last SQL statement
     */
    public static function execute($sql, $params = array())
    {
        $query = static::getConnection()->prepare($sql);
        foreach ($params as $key => $value) {
            $query->bindValue(is_numeric($key) ? $key + 1 : $key, $value);
        }

        $query->execute();

        return $query->rowCount();
    }

    public static function fetchColumn($sql, $params = array())
    {
        $query = static::getConnection()->prepare($sql);
        foreach ($params as $key => $value) {
            $query->bindValue(is_numeric($key) ? $key + 1 : $key, $value);
        }

        $query->execute();
        return $query->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Execute query, inserts values from assoc array
     * @param string $table
     * @param array $row
     * @return mixed
     */
    public static function insert($table, $row, $ignore = false)
    {
        $params = array();
        $_ignore = $ignore ? "IGNORE" : "";

        $sql = "INSERT {$_ignore} INTO `{$table}` SET ";

        foreach ($row as $column => $value) {
            $sql .= "`{$column}` = ?, ";
            $params[] = $value;
        }
        $sql = substr($sql, 0, -2);

        if (static::execute($sql, $params)) {
            $lastInsertId = static::getConnection()->lastInsertId();
            if ($lastInsertId === '0') { // for tables that do not have auto increment id
                return true;
            }
            return $lastInsertId;
        } else {
            return false;
        }
    }

    /**
     * @param string $table
     * @param array $condition pvz. array("user_id" => 5, "card_id" => 8)
     * @return type
     */
    public static function delete($table, $condition)
    {
        $sql = "DELETE FROM `{$table}` WHERE ";
        $params = array();
        foreach ($condition as $column => $value) {
            $sql .= "`{$column}` = ? AND ";
            $params[] = $value;
        }
        $sql = substr($sql, 0, -4);

        return static::execute($sql, $params);
    }

    /**
     * Execute query, updates values from assoc array
     * @param string $table
     * @param array $update
     * @param array|int $condition
     * @return int count of rows updated
     */
    public static function update($table, $update, $condition)
    {
        if (empty($update)) {
            return false;
        }

        $sql = "UPDATE `{$table}` SET ";
        $params = array();
        foreach ($update as $column => $value) {
            $sql .= "`{$column}` = ? , ";
            $params[] = $value;
        }
        $sql = substr($sql, 0, -2);

        $sql .= " WHERE ";

        if (is_array($condition)) {
            foreach ($condition as $column => $value) {
                $sql .= "`{$column}` = ? AND ";
                $params[] = $value;
            }
            $sql = substr($sql, 0, -4);
        } else {
            $sql .= " `id` = ? ";
            $params[] = $condition;
        }

        return static::execute($sql, $params);
    }

    public static function tablePrefix()
    {
        return static::$tablePrefix;
    }
}

}

namespace {

    function ipTable($name, $alias = '')
    {
        if (func_num_args() == 1) {
            $alias = $name;
        }

        $tableName = '`' . \Ip\Db::tablePrefix() . $name . '`';
        if ($alias) {
            $tableName = ' AS `' . $alias . '`';
        }

        return $tableName;
    }

}
