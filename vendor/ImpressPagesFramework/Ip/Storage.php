<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip;

/**
 * Key-value storage, where any plugin can store it's data
 */

class Storage
{

    /**
     * Get a value from storage
     *
     * @param string $pluginName Plugin name
     * @param string $key Option name
     * @param null $defaultValue Returned if specified key has no value assigned
     * @return mixed
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

        $params = array(
            ':plugin' => $pluginName,
            ':key' => $key
        );


        $row = ipDb()->fetchRow($sql, $params);
        if (!$row) {
            if ($defaultValue instanceof \Closure) {
                return $defaultValue();
            } else {
                return $defaultValue;
            }
        }

        return json_decode($row['value'], true);
    }


    /**
     * Set storage value
     *
     * @param string $pluginName Plugin name
     * @param string $key Option key
     * @param mixed $value Option value
     */
    public function set($pluginName, $key, $value)
    {
        $keys = array(
            'plugin' => $pluginName,
            'key' => $key
        );

        $values = array(
            'value' => json_encode($value)
        );

        ipDb()->upsert('storage', $keys, $values);
    }


    /**
     * Get all storage values for the plugin
     * @param string $plugin
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


        $params = array(
            ':plugin' => $plugin
        );

        $records = ipDb()->fetchAll($sql, $params);
        $values = array();

        foreach ($records as $record) {
            $values[] = array(
                'key' => $record['key'],
                'value' => json_decode($record['value'], true)
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

        $params = array(
            ':plugin' => $pluginName,
            ':key' => $key
        );

        ipDb()->execute($sql, $params);

    }
    
    /**
     * Remove all storage values for the plugin
     *
     * @param string $pluginName Plugin name
     */
    public function removeAll($pluginName)
    {
        $sql = '
            DELETE FROM
                ' . ipTable('storage') . '
            WHERE
                `plugin` = :plugin
        ';

        $params = array(
            ':plugin' => $pluginName
        );

        ipDb()->execute($sql, $params);

    }

}
