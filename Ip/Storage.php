<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip;

/**
 * Key-value storage, where any plugin can store it's data
 */

class Storage {

    /**
     * Get a value from CMS storage
     *
     * @param string $pluginName Plugin name
     * @param string $key Option name
     * @param null $defaultValue Returned if specified key has no value assigned
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


        $jsonValue = ipDb()->fetchValue($sql, $params);
        if ($jsonValue === false) {
            return $defaultValue;
        }

        return json_decode($jsonValue, TRUE);
    }


    /**
     * Set CMS storage value
     *
     * @param string $pluginName Plugin name
     * @param string $key Option key
     * @param $value Option value
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
            ':value' => json_encode($value)
        );

        ipDb()->execute($sql, $params);
    }

    /**
     * Get all storage values for the plugin
     *
     * @param string $pluginName Plugin name
     * @return array Key=>value array of plugin options
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

        $records = ipDb()->fetchAll($sql, $params);
        $values = array();

        foreach($records as $record) {
            $values[] = array(
                'key' => $record['key'],
                'value' => json_decode($record['value'], TRUE)
            );
        }
        return $values;
    }

    /**
     * Remove storage key for specific plugin
     *
     * @param string $pluginName Plugin name
     * @param string $key Key to remove
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
