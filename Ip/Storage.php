<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip;




class Storage {

    /**
     * @param string $pluginName
     * @param string $key option name
     * @param null $defaultValue
     * @return string
     */
    public static function get($pluginName, $key, $defaultValue = null)
    {

        $sql = '
            SELECT
                value
            FROM
                `'.DB_PREF.'storage`
            WHERE
                `plugin` = :plugin AND
                `key` = :key
        ';

        $params = array (
            ':plugin' => $pluginName,
            ':key' => $key
        );


        $value = \Ip\Db::fetchValue($sql, $params);

        if ($value === null) {
            return $defaultValue;
        }

        return $value;
    }


    /**
     * @param $pluginName
     * @param $key
     * @param $value
     */
    public static function set($pluginName, $key, $value)
    {

        $sql = '
            INSERT INTO
                `'.DB_PREF.'storage`
            SET
                `plugin` = :plugin,
                `key` = :key,
                `value` = :value
            ON DUPLICATE KEY UPDATE
                `plugin` = :plugin,
                `key` = :key,
                `value` = :value
        ';

        $params = array (
            ':plugin' => $pluginName,
            ':key' => $key,
            ':value' => $value
        );

        \Ip\Db::execute($sql, $params);
    }




}