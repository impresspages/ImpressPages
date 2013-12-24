<?php

use \Ip\Db;

function ip_deprecated_mysql_query($query)
{
    global $ip_deprecated_mysql_last_rs, $ip_deprecated_mysql_error;

    try {
        $pdo = ipDb()->getConnection();
        $ip_deprecated_mysql_last_rs = $pdo->query($query);
        $ip_deprecated_mysql_error = null;
        return $ip_deprecated_mysql_last_rs;
    } catch (\PDO_Exception $pe) {
        $ip_deprecated_mysql_error = $pe->getMessage();
    } catch (\Exception $e) {
        $ip_deprecated_mysql_error = $e->getMessage();
    }
}

function ip_deprecated_mysql_real_escape_string($unescaped_string)
{
    $pdo = ipDb()->getConnection();
    $result = trim($pdo->quote($unescaped_string), "'");

    return $result;
}

/**
 * @param \PDOStatement $result
 * @return mixed
 */
function ip_deprecated_mysql_fetch_assoc($result)
{
    return $result->fetch(\PDO::FETCH_ASSOC);
}

function ip_deprecated_mysql_error()
{
    global $ip_deprecated_mysql_error;
    return isset($ip_deprecated_mysql_error) ? $ip_deprecated_mysql_error : null;
}

function ip_deprecated_mysql_insert_id()
{
    $pdo = ipDb()->getConnection();
    return $pdo->lastInsertId();
}
