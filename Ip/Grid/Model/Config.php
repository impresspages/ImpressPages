<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid\Model;


class Config
{
    protected $config = null;

    /**
     * @var Field[]
     */
    protected $fieldObjects = null;

    public function __construct($config)
    {
        $this->config = $config;

        if (empty($this->config['table'])) {
            throw new \Ip\CoreException('\'table\' configuration value missing.');
        }

        if (empty($this->config['fields'])) {
            $this->config['fields'] = $this->getTableFields($this->config['table']);
        }

        if (empty($this->config['idField'])) {
            $this->config['idField'] = 'id';
        }

        if (empty($this->config['pageSize'])) {
            $this->config['pageSize'] = 10;
        }

        foreach ($this->config['fields'] as &$field) {
            if (empty($field['type'])) {
                $field['type'] = 'Text';
            }
        }
    }

    public function getRaw($key) {
        if (!empty($this->config[$key])) {
            return $this->config[$key];
        }
        return null;
    }

    public function deleteWarning()
    {
        $warning = $this->getRaw('deleteWarning');
        if (empty($warning)) {
            $warning = __('Are you sure you want to delete?', 'ipAdmin', FALSE);
        }
        return $warning;
    }

    public function actions()
    {
        if (!empty($this->config['actions'])) {
            return $this->config['actions'];
        }
        return array();
    }

    public function beforeDelete()
    {
        if (empty($this->config['beforeDelete'])) {
            return FALSE;
        }
        return $this->config['beforeDelete'];
    }

    public function afterDelete()
    {
        if (empty($this->config['afterDelete'])) {
            return FALSE;
        }
        return $this->config['afterDelete'];
    }

    public function preventAction()
    {
        if (empty($this->config['preventAction'])) {
            return FALSE;
        }
        return $this->config['preventAction'];
    }

    /**
     * @return \Ip\Grid\Model\Field[]
     */
    public function fieldObject($field)
    {
        if (empty($field['type'])) {
            $field['type'] = 'Text';
        }
        $class = '\\Ip\\Grid\\Model\\Field\\' . $field['type'];
        if (!class_exists($class)) {
            $class = $field['type']; //type is full class name
        }
        $fieldObject = new $class($field);
        return $fieldObject;
    }

    public function fields()
    {
        return $this->config['fields'];
    }

    public function allowInsert()
    {
        return !array_key_exists('allowInsert', $this->config) || $this->config['allowInsert'];
    }

    public function allowSearch()
    {
        return !array_key_exists('allowSearch', $this->config) || $this->config['allowSearch'];
    }

    public function allowUpdate()
    {
        return !array_key_exists('allowUpdate', $this->config) || $this->config['allowUpdate'];
    }

    public function allowDelete()
    {
        return !array_key_exists('allowDelete', $this->config) || $this->config['allowDelete'];
    }

    public function pageSize()
    {
        return $this->config['pageSize'];
    }

    public function idField()
    {
        return $this->config['idField'];
    }

    public function tableName()
    {
        return ipTable(str_replace("`", "", $this->config['table']));
    }
}