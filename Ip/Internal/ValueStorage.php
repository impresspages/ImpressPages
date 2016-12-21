<?php


namespace Ip\Internal;

/**
 * Value storage saves any php value.
 */
abstract class ValueStorage extends RawStorage
{
    /**
     * Get a value from storage
     *
     * @param string $key Key name
     * @param null $defaultValue Returned if specified key has no value assigned
     * @return string
     */
    public function get($key, $defaultValue = null)
    {
        $value = ipDb()->selectValue(
            $this->tableName,
            array($this->valueColumn),
            array($this->namespaceColumn => $this->namespace, $this->keyColumn => $key)
        );
        return ($value === null) ? $defaultValue : json_decode($value, true);
    }

    /**
     * Set storage value
     *
     * @param string $key Key name
     * @param mixed $value Value
     */
    public function set($key, $value)
    {
        parent::set($key, json_encode($value));
    }

    /**
     * Get all storage values
     *
     * @return array Key=>value array of all storage values
     */
    public function getAll()
    {
        $values = ipDb()->selectAll(
            $this->tableName,
            array($this->keyColumn, $this->valueColumn),
            array($this->namespaceColumn => $this->namespace)
        );

        $result = [];
        foreach ($values as $value) {
            $result[$value[$this->keyColumn]] = json_decode($value[$this->valueColumn], true);
        }
        return $result;
    }
}
