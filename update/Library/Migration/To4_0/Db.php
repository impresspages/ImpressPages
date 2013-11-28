<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace IpUpdate\Library\Migration\To4_0;

/**
 *
 * Create and supply services to modules
 *
 */
class Db
{

    static $conn;

    public static function init($conn)
    {
        self::$conn = $conn;
    }

    public static function getConnection()
    {
        return static::$conn;
    }


    public static function fetchValue($sql, $params = array())
    {
        $query = static::getConnection()->prepare($sql . " LIMIT 1");
        foreach ($params as $key => $value) {
            $query->bindValue(is_numeric($key) ? $key + 1 : $key, $value);
        }

        $query->execute();
        return $query->fetchColumn(0);
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
     * @param array $condition pvz. array("userId" => 5, "card_id" => 8)
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

