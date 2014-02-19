<?php
/**
 * @package ImpressPages
 */

namespace Ip\Internal;

abstract class RawStorage
{
    protected $tableName = '';
    protected $namespaceColumn = 'namespace';
    protected $keyColumn = 'key';
    protected $valueColumn = 'value';
    protected $namespace;

    protected $fullTableName;

    public function __construct($namespace)
    {
        $this->namespace = $namespace;

        if (empty($this->tableName)) {
            throw new \Ip\Exception('Storage table name is not defined.');
        }
        $this->fullTableName = ipTable($this->tableName);
    }

    /**
     * Get a value from CMS storage
     *
     * @param string $key       Key name
     * @param null $defaultValue Returned if specified key has no value assigned
     * @return string
     */
    public function get($key, $defaultValue = null)
    {
        $value = ipDb()->selectValue($this->tableName, array($this->valueColumn), array($this->namespaceColumn => $this->namespace, $this->keyColumn => $key));
        return ($value === null) ? $defaultValue : $value;
    }

    /**
     * Set CMS storage value
     *
     * @param string $key       Key name
     * @param $value            Value
     */
    public function set($key, $value)
    {
        $sql = "
            INSERT INTO
                {$this->fullTableName}
            SET
                `{$this->namespaceColumn}` = :namespace,
                `{$this->keyColumn}` = :key,
                `{$this->valueColumn}` = :value
            ON DUPLICATE KEY UPDATE
                `{$this->namespaceColumn}` = :namespace,
                `{$this->keyColumn}` = :key,
                `{$this->valueColumn}` = :value
        ";

        $params = array(
            ':namespace' => $this->namespace,
            ':key' => $key,
            ':value' => json_encode($value)
        );

        ipDb()->execute($sql, $params);
    }

    /**
     * Get all storage values
     *
     * @return array Key=>value array of plugin options
     */
    public function getAll()
    {
        $values = ipDb()->selectAll($this->tableName, array($this->keyColumn, $this->valueColumn), array($this->namespaceColumn => $this->namespace));

        $result = array();
        foreach ($values as $value) {
            $result[$value[$this->keyColumn]] = $value[$this->valueColumn];
        }
        return $result;
    }

    /**
     * Remove storage key
     *
     * @param string $key Key to remove
     */
    public function remove($key)
    {
        ipDb()->delete($this->tableName, array($this->namespaceColumn => $this->namespace, $this->keyColumn => $key));
    }

    /**
     * Remove all
     */
    public function removeAll()
    {
        ipDb()->delete($this->tableName, array($this->namespaceColumn => $this->namespace));
    }

}
