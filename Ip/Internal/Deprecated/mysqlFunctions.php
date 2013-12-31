<?php

use \Ip\Db;



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

