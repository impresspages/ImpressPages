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
    public function get($pluginName, $key, $defaultValue = null)
    {

        $sql = '
            SELECT
                value
            FROM
                ' . ipTable('storage') . '
            WHERE
                `plugin` = :plugin AND
                `key` = :key
        ';

        $params = array (
            ':plugin' => $pluginName,
            ':key' => $key
        );


        $value = ipDb()->fetchValue($sql, $params);

        if ($value === false) {
            return $defaultValue;
        }

        return $value;
    }


    /**
     * @param $pluginName
     * @param $key
     * @param $value
     */
    public function set($pluginName, $key, $value)
    {

        $sql = '
            INSERT INTO
                '.ipTable('storage').'
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

        ipDb()->execute($sql, $params);
    }

    /**
     * @param $pluginName
     * @param $key
     * @return array
     */
    public function getAll($plugin)
    {

        $sql = '
            SELECT
                `key`, `value`
            FROM
                ' . ipTable('storage') . '
            WHERE
                `plugin` = :plugin
            ';


        $params = array (
            ':plugin' => $plugin
        );

        return ipDb()->fetchAll($sql, $params);
    }

    /**
     * @param $pluginName
     * @param $key
     */
    public function remove($pluginName, $key)
    {
        $sql = '
            DELETE FROM
                ' . ipTable('storage') . '
            WHERE
                `plugin` = :plugin
                AND
                `key` = :key
        ';

        $params = array (
            ':plugin' => $pluginName,
            ':key' => $key
        );

        ipDb()->execute($sql, $params);

    }

}