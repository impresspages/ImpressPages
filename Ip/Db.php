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

    public function __construct($pdo = null)
    {
        if ($pdo) {
            $this->pdoConnection = $pdo;
        } else {
            $this->getConnection();
        }
    }

    /**
     * Get database connection object
     *
     * @throws \Ip\Exception\Db
     * @return \PDO
     */
    public function getConnection()
    {
        if ($this->pdoConnection) {
            return $this->pdoConnection;
        }

        $dbConfig = ipConfig()->get('db');
        ipConfig()->set('db', null);

        if (empty($dbConfig)) {
            throw new \Ip\Exception\Db("Can't connect to database. No connection config found or \\Ip\\Db::disconnect() has been used.");
        }

        try {
            if (array_key_exists('driver', $dbConfig) && $dbConfig['driver'] == 'sqlite') {
                $dsn = 'sqlite:' . $dbConfig['database'];
                $this->pdoConnection = new \PDO($dsn);
                $this->pdoConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } else {
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
            }

        } catch (\PDOException $e) {
            throw new \Ip\Exception($e->getMessage());
            //PHP traces all details of error including DB password. This could be a disaster on live server. So we hide that data.
        }

        $this->tablePrefix = $dbConfig['tablePrefix'];

        return $this->pdoConnection;
    }


    public function setConnection($connection)
    {
        $this->pdoConnection = $connection;
    }

    /**
     * Disconnect from the database
     */
    public function disconnect()
    {
        $this->pdoConnection = null;
    }

    /**
     * Execute SQL query and fetch a value from the result set.
     *
     * @param string $sql
     * @param array $params
     * @return string|null
     * @throws \Ip\Exception\Db
     */
    public function fetchValue($sql, $params = array())
    {
        try {
            $query = $this->getConnection()->prepare($sql . " LIMIT 1");
            foreach ($params as $key => $value) {
                if (is_bool($value)) {
                    $value = $value ? 1 : 0;
                }
                $query->bindValue(is_numeric($key) ? $key + 1 : $key, $value);
            }

            $query->execute();
            $result = $query->fetchColumn(0);
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            throw new \Ip\Exception\Db($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Execute SQL query and fetch a single row from the result set
     *
     * @param $sql
     * @param array $params
     * @return array|null
     * @throws \Ip\Exception\Db
     */

    public function fetchRow($sql, $params = array())
    {
        try {
            $query = $this->getConnection()->prepare($sql . " LIMIT 1");
            foreach ($params as $key => $value) {
                if (is_bool($value)) {
                    $value = $value ? 1 : 0;
                }
                $query->bindValue(is_numeric($key) ? $key + 1 : $key, $value);
            }

            $query->execute();
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);

            return $result ? $result[0] : null;
        } catch (\PDOException $e) {
            throw new \Ip\Exception\Db($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Execute SQL query and fetch all query results
     *
     * @param $sql
     * @param array $params
     * @return array
     * @throws \Ip\Exception\Db
     */
    public function fetchAll($sql, $params = array())
    {
        try {
            $query = $this->getConnection()->prepare($sql);
            foreach ($params as $key => $value) {
                if (is_bool($value)) {
                    $value = $value ? 1 : 0;
                }
                $query->bindValue(is_numeric($key) ? $key + 1 : $key, $value);
            }

            $query->execute();


            $result = $query->fetchAll(\PDO::FETCH_ASSOC);

            return $result ? $result : array();
        } catch (\PDOException $e) {
            throw new \Ip\Exception\Db($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Execute SELECT query on specified table and return array with results
     *
     * @param string $table Table name without prefix
     * @param array|string $columns list of columns or string. For example array('id', 'name') or '*'.
     * @param array $where Conditional array. For example array('id' => 20)
     * @param string $sqlEnd SQL string appended at the end of the query. For example 'ORDER BY `createdAt` DESC'
     * @return array
     */
    public function selectAll($table, $columns, $where = array(), $sqlEnd = '')
    {
        if (is_array($columns)) {
            $columns = '`' . implode('`,`', $columns) . '`';
        }

        $sql = 'SELECT ' . $columns . ' FROM ' . ipTable($table);

        $params = array();

        $sql .= " WHERE " . $this->buildConditions($where, $params) . " ";

        if ($sqlEnd) {
            $sql .= $sqlEnd;
        }

        return $this->fetchAll($sql, $params);
    }

    /**
     * Execute SELECT query and return a single row
     * @see self::selectAll()
     *
     * @param string $table Table name without prefix
     * @param array|string $columns List of columns as array or string. For example array('id', 'name') or '*'.
     * @param array $where Conditional array. For example array('id' => 20)
     * @param string $sqlEnd SQL string appended at the end of the query. For example 'ORDER BY `createdAt` DESC'
     * @return array|null
     */
    public function selectRow($table, $columns, $where, $sqlEnd = '')
    {
        $result = $this->selectAll($table, $columns, $where, $sqlEnd . ' LIMIT 1');
        return $result ? $result[0] : null;
    }

    /**
     * Execute SELECT query and return a single value
     *
     * @see self::selectAll()
     *
     * @param string $table Table name without prefix
     * @param string $column Column name. For example 'id'.
     * @param array $where Conditional array. For example array('id' => 20)
     * @param string $sqlEnd SQL string appended at the end of the query. For example 'ORDER BY `createdAt` DESC'
     * @return mixed|null
     */
    public function selectValue($table, $column, $where, $sqlEnd = '')
    {
        $result = $this->selectAll($table, $column, $where, $sqlEnd . ' LIMIT 1');
        return $result ? array_shift($result[0]) : null;
    }

    public function selectColumn($table, $column, $where, $sqlEnd = '')
    {
        $sql = 'SELECT ' . $column . ' FROM ' . ipTable($table);

        $params = array();
        $sql .= ' WHERE ';
        if ($where) {
            foreach ($where as $column => $value) {
                if ($value === null) {
                    $sql .= "`{$column}` IS NULL AND ";
                } else {
                    $sql .= "`{$column}` = ? AND ";
                    if (is_bool($value)) {
                        $value = $value ? 1 : 0;
                    }
                    $params[] = $value;
                }
            }

            $sql = substr($sql, 0, -4);
        } else {
            $sql .= ' 1 ';
        }

        if ($sqlEnd) {
            $sql .= $sqlEnd;
        }

        try {
            $query = $this->getConnection()->prepare($sql);
            foreach ($params as $key => $value) {
                if (is_bool($value)) {
                    $value = $value ? 1 : 0;
                }
                $query->bindValue($key + 1, $value);
            }

            $query->execute();
            return $query->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            throw new \Ip\Exception\Db($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Execute SQL query
     *
     * @param string $sql
     * @param array $params
     * @return int The number of rows affected by the last SQL statement
     * @throws \Ip\Exception\Db
     */
    public function execute($sql, $params = array())
    {
        try {
            $query = $this->getConnection()->prepare($sql);
            foreach ($params as $key => $value) {
                if (is_bool($value)) {
                    $value = $value ? 1 : 0;
                }
                $query->bindValue(is_numeric($key) ? $key + 1 : $key, $value);
            }

            $query->execute();

            return $query->rowCount();
        } catch (\PDOException $e) {
            throw new \Ip\Exception\Db($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Execute SQL query and return a result set
     *
     * @param string $sql query
     * @param array $params The array represents each row as either an array of column values.
     * @return array
     * @throws \Ip\Exception\Db
     */
    public function fetchColumn($sql, $params = array())
    {
        try {
            $query = $this->getConnection()->prepare($sql);
            foreach ($params as $key => $value) {
                if (is_bool($value)) {
                    $value = $value ? 1 : 0;
                }
                $query->bindValue(is_numeric($key) ? $key + 1 : $key, $value);
            }

            $query->execute();
            return $query->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            throw new \Ip\Exception\Db($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * Execute query, insert values from associative array
     * @param $table
     * @param $row
     * @param bool $ignore
     * @return bool|string
     */
    public function insert($table, $row, $ignore = false)
    {
        $params = array();
        $values = '';
        $_ignore = $ignore ? ($this->isSqlite()?"OR IGNORE":"IGNORE") : "";

        $sql = "INSERT {$_ignore} INTO " . ipTable($table) . " (";

        foreach ($row as $column => $value) {
            $sql .= "`{$column}`, ";
            if (is_bool($value)) {
                $value = $value ? 1 : 0;
            }
            $params[] = $value;
            $values.='?, ';
        }
        $sql = substr($sql, 0, -2);
        $values = substr($values, 0, -2);
        $sql .= ") VALUES (${values})";

        if (empty($params)) {
            $sql = "INSERT {$_ignore} INTO " . ipTable($table) . " () VALUES()";
        }


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
     * Delete rows from a table
     *
     * @param string $table
     * @param array $condition A condition, for example, array("userId" => 5, "card_id" => 8)
     * @return int count of rows affected
     */
    public function delete($table, $condition)
    {
        $sql = "DELETE FROM " . ipTable($table, false) . " WHERE ";
        $params = array();

        $sql .= $this->buildConditions($condition, $params);

        return $this->execute($sql, $params);
    }

    /**
     * Update table records
     *
     * Execute query, updates values from associative array
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

        $sql = 'UPDATE ' . ipTable($table) . ' SET ';
        $params = array();
        foreach ($update as $column => $value) {
            $sql .= "`{$column}` = ? , ";
            if (is_bool($value)) {
                $value = $value ? 1 : 0;
            }
            $params[] = $value;
        }
        $sql = substr($sql, 0, -2);

        $sql .= " WHERE ";
        $sql .= $this->buildConditions($condition, $params);

        return $this->execute($sql, $params);
    }

    /**
     * insert or update row of $table identified by $keys with $values
     *
     * @param string $table
     * @param array $keys
     * @param array $values
     */
    public function upsert($table, $keys, $values) {
        if ($this->insert($table, array_merge($keys, $values), true) == false) {
            $this->update($table, $values, $keys);
        }
    }

    /**
     * Return table prefix
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

    /**
     * Return name of current driver
     */
    public function getDriverName()
    {
        return $this->pdoConnection->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }


    /**
     * Return true if database is sqlite
     *
     * @return bool
     */
    public function isSQLite()
    {
        return $this->getDriverName() == 'sqlite';
    }

    /**
     * Return true if database is mysql
     *
     * @return bool
     */
    public function isMySQL()
    {
        return $this->getDriverName() == 'mysql';
    }

    /**
     * Return SQL condition to select rows with minimum age (database-dependent)
     *
     * @param string $fieldName field to compare
     * @param int    $minAge    minimum age
     * @param string $unit      unit for age (HOUR or MINUTE)
     * @return string           sql condition
     */
    public function sqlMinAge($fieldName, $minAge, $unit='HOUR') {
        if (!in_array($unit, array('MINUTE', 'HOUR'))) {
            throw \Ip\Exception("Only 'MINUTE' or 'HOUR' are available as unit options.");
        }

        if (ipDb()->isMySQL()) {
            $sql = "`".$fieldName."` < NOW() - INTERVAL " . ((int)$minAge) . " ".$unit;
        } else {
            $divider = 1;
            switch($unit) {
                case 'HOUR':
                    $divider = 60*60;
                    break;
                case 'MINUTE':
                    $divider = 60;
                    break;
            }
            $sql = "((STRFTIME('%s', 'now', 'localtime') - STRFTIME('%s', `".$fieldName.
                "`)/".$divider.")>". ((int)$minAge). ") ";
        }

        return $sql;
    }

    /**
     * Return SQL condition to select rows with minimum age (database-dependent)
     *
     * @param string $fieldName field to compare
     * @param int    $maxAge    minimum age
     * @param string $unit      unit for age (HOUR or MINUTE)
     * @return string           sql condition
     */
    public function sqlMaxAge($fieldName, $maxAge, $unit='HOUR') {
        if (!in_array($unit, array('MINUTE', 'HOUR'))) {
            throw \Ip\Exception("Only 'MINUTE' or 'HOUR' are available as unit options.");
        }

//        SELECT DATE(NOW()-INTERVAL 15 DAY)
        if (ipDb()->isMySQL()) {
            $sql = "`".$fieldName."` > NOW() - INTERVAL " . ((int)$maxAge) . " ".$unit;
        } else {
            switch($unit) {
                case 'HOUR':
                    $divider = 60*60;
                    break;
                case 'MINUTE':
                    $divider = 60;
                    break;
            }
            $sql = "((STRFTIME('%s', 'now', 'localtime') - STRFTIME('%s', `".$fieldName.
                "`)/".$divider.")>". ((int)$maxAge). ") ";
        }

        return $sql;
    }

    /**
     * Build WHERE statement from conditions.
     *
     * @param array $conditions
     * @param array $params
     *
     * @return string
     */
    protected function buildConditions($conditions = array(), &$params = array())
    {
        if (empty($conditions)) {
            return '1';
        }

        $sql = '';
        if (is_array($conditions)) {
            foreach ($conditions as $column => $value) {
                $realCol = $column;
                $pair = $this->containsOperator($column);

                if ($pair) {
                    $realCol = $pair[0];
                }

                if ($value === null) {
                    $isNull = 'IS NULL AND';
                    if ($pair && preg_match("/(<>|!=)/", $pair[1])) {
                        $isNull = 'IS NOT NULL AND';
                    }

                    $sql .= "`{$realCol}` {$isNull} ";
                } else {
                    if ($pair) {
                        $sql .= "`{$realCol}` {$pair[1]} ? AND ";
                    } else {
                        $sql .= "`{$realCol}` = ? AND ";
                    }

                    if (is_bool($value)) {
                        $value = $value ? 1 : 0;
                    }

                    $params[] = $value;
                }
            }
            $sql = substr($sql, 0, -4);
        } else {
            $sql .= " `id` = ? ";
            $params[] = $conditions;
        }

        return $sql;
    }

    /**
     * Check whether a value contains an operator.
     *
     * @param $value string
     *
     * @return array|bool
     */
    protected function containsOperator($value)
    {
        $idents = preg_split("/(<=>|>=|<=|<>|>|<|!=|=|LIKE)/", $value, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if (count($idents) <= 1) {
            return false;
        } else {
            return array_map('trim', $idents);
        }
    }

}
