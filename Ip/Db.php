<?php
/**
 *
 * @package ImpressPages
 *
 */

namespace Ip;

/**
 *  Database connector
 *
 */
class Db
{
    /**
     * @var \PDO
     */
    protected $pdoConnection;

    protected $tablePrefix;

    public function __construct()
    {
        $this->getConnection();
    }

    /**
     * Get database connection object
     *
     * @throws \Ip\Exception
     * @return \PDO
     */
    public function getConnection()
    {
        if ($this->pdoConnection) {
            return $this->pdoConnection;
        }

        $dbConfig = ipConfig()->getRaw('db');

        if (empty($dbConfig)) {
            throw new \Ip\Exception\Db("Can't connect to database. No connection config found or \\Ip\\Db::disconnect() has been used.");
        }

        try {


            $dsn = 'mysql:host=' . str_replace(':', ';port=', $dbConfig['hostname']);
            if (!empty($dbConfig['database'])) {
                $dsn .= ';dbname=' . $dbConfig['database'];
            }

            $this->pdoConnection = new \PDO($dsn, $dbConfig['username'], $dbConfig['password']);
            $this->pdoConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $dt = new \DateTime();
            $offset = $dt->format("P");
            $this->pdoConnection->exec("SET time_zone='$offset';");
            $this->pdoConnection->exec("SET CHARACTER SET " . $dbConfig['charset']);
        } catch (\PDOException $e) {
            throw new \Ip\Exception\Db("Can't connect to database. Stack trace hidden for security reasons");
            //PHP traces all details of error including DB password. This could be a disaster on live server. So we hide that data.
        }

        $this->tablePrefix = $dbConfig['tablePrefix'];
        ipConfig()->_setRaw('db', null);

        return $this->pdoConnection;
    }

    /**
     * Disconnect from the database
     */
    public function disconnect()
    {
        ipConfig()->_setRaw('db', null);
        $this->pdoConnection = null;
    }

    /**
     * Execute SQL query and fetch a value from the result set.
     *
     * @param string $sql
     * @param array $params
     * @return string
     * @throws DbException
     */
    public function fetchValue($sql, $params = array())
    {
        try {
            $query = $this->getConnection()->prepare($sql . " LIMIT 1");
            foreach ($params as $key => $value) {
                $query->bindValue(is_numeric($key) ? $key + 1 : $key, $value);
            }

            $query->execute();
            return $query->fetchColumn(0);
        } catch (\PDOException $e) {
            throw new DbException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Execute SQL query and fetch a single row from the result set
     *
     * @param $sql
     * @param array $params
     * @return null
     * @throws DbException
     */

    public function fetchRow($sql, $params = array())
    {
        try {
            $query = $this->getConnection()->prepare($sql . " LIMIT 1");
            foreach ($params as $key => $value) {
                $query->bindValue(is_numeric($key) ? $key + 1 : $key, $value);
            }

            $query->execute();
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);

            return $result ? $result[0] : null;
        } catch (\PDOException $e) {
            throw new DbException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Execute SQL query and fetch all query results
     *
     * @param $sql
     * @param array $params
     * @return array
     * @throws DbException
     */
    public function fetchAll($sql, $params = array())
    {
        try {
            $query = $this->getConnection()->prepare($sql);
            foreach ($params as $key => $value) {
                $query->bindValue(is_numeric($key) ? $key + 1 : $key, $value);
            }

            $query->execute();


            $result = $query->fetchAll(\PDO::FETCH_ASSOC);

            return $result ? $result : array();
        } catch (\Exception $e) {
            throw new DbException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Execute SELECT query on specified table and return array with results
     *
     * @param string $fields comma separated string containing a list of fields
     * @param $table
     * @param $where
     * @param string $sqlEnd
     * @return array
     */
    public function select($fields, $table, $where, $sqlEnd = '')
    {
        $sql = 'SELECT ' . $fields . ' FROM ' . ipTable($table) . ' WHERE ';

        $params = array();
        foreach ($where as $column => $value) {
            if ($value === NULL) {
                $sql .= "`{$column}` IS NULL AND ";
            } else {
                $sql .= "`{$column}` = ? AND ";
                $params[] = $value;
            }
        }
        if ($where) {
            $sql = substr($sql, 0, -4);
        } else {
            $sql .= '1 ';
        }

        if ($sqlEnd) {
            $sql .= ' ' . $sqlEnd;
        }

        return $this->fetchAll($sql, $params);
    }

    /**
     * Execute SQL query
     *
     * @param string $sql
     * @param array $params
     * @return int the number of rows affected by the last SQL statement
     */
    public function execute($sql, $params = array())
    {
        try {
            $query = $this->getConnection()->prepare($sql);
            foreach ($params as $key => $value) {
                $query->bindValue(is_numeric($key) ? $key + 1 : $key, $value);
            }

            $query->execute();

            return $query->rowCount();
        } catch (\PDOException $e) {
            throw new DbException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Execute SQL query and return a result set
     *
     * @param string $sql query
     * @param array $params The array represents each row as either an array of column values.
     * @return array
     * @throws DbException
     */
    public function fetchColumn($sql, $params = array())
    {
        try {
            $query = $this->getConnection()->prepare($sql);
            foreach ($params as $key => $value) {
                $query->bindValue(is_numeric($key) ? $key + 1 : $key, $value);
            }

            $query->execute();
            return $query->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            throw new DbException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Execute query, insert values from assoc array
     *
     * @param string $table
     * @param array $row
     * @return mixed
     */
    public function insert($table, $row, $ignore = false)
    {
        $params = array();
        $_ignore = $ignore ? "IGNORE" : "";

        $sql = "INSERT {$_ignore} INTO " . ipTable($table) . " SET ";

        foreach ($row as $column => $value) {
            $sql .= "`{$column}` = ?, ";
            $params[] = $value;
        }
        $sql = substr($sql, 0, -2);

        if ($this->execute($sql, $params)) {
            $lastInsertId = $this->getConnection()->lastInsertId();
            if ($lastInsertId === '0') { // for tables that do not have auto increment id
                return true;
            }
            return $lastInsertId;
        } else {
            return false;
        }
    }

    /**
     * Delete a table
     *
     * @param string $table
     * @param array $condition for example, array("userId" => 5, "card_id" => 8)
     * @return type
     */
    public function delete($table, $condition)
    {
        $sql = "DELETE FROM " . ipTable($table) . " WHERE ";
        $params = array();
        foreach ($condition as $column => $value) {
            if ($value === NULL) {
                $sql .= "`{$column}` IS NULL AND ";
            } else {
                $sql .= "`{$column}` = ? AND ";
                $params[] = $value;
            }
        }
        $sql = substr($sql, 0, -4);

        return $this->execute($sql, $params);
    }

    /**
     * Update table records
     *
     * Execute query, updates values from assoc array
     * @param string $table
     * @param array $update
     * @param array|int $condition
     * @return int count of rows updated
     */
    public function update($table, $update, $condition)
    {
        if (empty($update)) {
            return false;
        }

        $sql = "UPDATE " . ipTable($table) . " SET ";
        $params = array();
        foreach ($update as $column => $value) {
            $sql .= "`{$column}` = ? , ";
            $params[] = $value;
        }
        $sql = substr($sql, 0, -2);

        $sql .= " WHERE ";

        if (is_array($condition)) {
            foreach ($condition as $column => $value) {
                if ($value === NULL) {
                    $sql .= "`{$column}` IS NULL AND ";
                } else {
                    $sql .= "`{$column}` = ? AND ";
                    $params[] = $value;
                }
            }
            $sql = substr($sql, 0, -4);
        } else {
            $sql .= " `id` = ? ";
            $params[] = $condition;
        }

        return $this->execute($sql, $params);
    }

    /**
     * Return CMS table prefix
     * @return mixed
     */
    public function tablePrefix()
    {
        return $this->tablePrefix;
    }

    /**
     * Get connection status
     *
     * @return bool
     */
    public function isConnected()
    {
        return $this->pdoConnection ? true : false;
    }
}
