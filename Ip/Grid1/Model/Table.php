<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid1\Model;


class Table extends \Ip\Grid1\Model{

    protected $config = null;
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

        foreach($this->config['fields'] as &$field) {
            if (empty($field['type'])) {
                $field['type'] = 'Text';
            }
        }
    }

    /**
     * @return \Ip\Grid1\Model\Field[]
     */
    protected function getFieldObjects()
    {
        if ($this->fieldObjects === null) {
            $collection = array();
            foreach ($this->config['fields'] as $field) {
                if ($field['type']) {
                    $class = '\Ip\Grid1\Model\Field\\' . $field['type'];
                    if (!class_exists($class)) {
                        $class = $field['type']; //type is full class name
                    }
                    $fieldObject = new $class($field);
                    $collection[] = $fieldObject;
                }
            }
            $this->fieldObjects = $collection;
        }
        return $this->fieldObjects;
    }


    public function handleMethod(\Ip\Request $request)
    {
        $data = $request->getRequest();
        if (empty($data['method'])) {
            throw new \Ip\CoreException('Missing request data');
        }
        $method = $data['method'];
        if (!isset($data['params'])) {
            $data['params'] = array();
        }

        switch($method) {
            case 'init':
                return $this->refresh();
                break;
        }

    }


    protected function getTableFields($tableName)
    {
        $sql = "SHOW COLUMNS FROM `".str_replace("`","", $tableName)."`";

        $fields = ipDb()->fetchColumn($sql);

        $result = array();
        foreach($fields as $fieldName) {
            $result[] = array(
                'label' => $fieldName,
                'field' => $fieldName
            );
        }

        return $result;
    }

    protected function fetch()
    {
        $sql = "
        SELECT
          *
        FROM
          `".str_replace("`","", $this->config['table'])."`
        WHERE
          1
        ";

        $result = ipDb()->fetchAll($sql);

        return $result;
    }

    protected function prepareData($data)
    {
        $preparedData = array();
        foreach($data as $row) {
            $preparedRow = array();
            foreach($this->getFieldObjects() as $key => $field) {
                $preview = $field->preview($row);
                if (!empty($this->config['fields'][$key]['filter'])) {
                    $filters = $this->config['fields'][$key]['filter'];
                    if (!is_array($filters)) {
                        $filters = array($filters);
                    }
                    foreach($filters as $filter) {
                        if (substr($filter, 1, 1) !== '\\') {
                            $filter = '\\' . $filter;
                        }
                        $preview = call_user_func($filter, $preview, $row);
                    }
                }
                $preparedRow[] = $preview;
            }
            $preparedData[] = $preparedRow;
        }
        return $preparedData;
    }

    protected function getFieldLabels()
    {
        $labels = array();
        foreach ($this->config['fields'] as $field) {
            $labels[] = $field['label'];
        }
        return $labels;
    }


    protected function refresh()
    {
        $variables = array(
            'labels' => $this->getFieldLabels(),
            'data' => $this->prepareData($this->fetch())
        );
        $html = \Ip\View::create('../view/table.php', $variables)->render();
        $commands = array(
            $this->commandSetHtml($html)
        );
        return $commands;
    }

}