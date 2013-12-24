<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid\Model;


class Table extends \Ip\Grid\Model
{

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

        if (empty($this->config['pageSize'])) {
            $this->config['pageSize'] = 10;
        }

        foreach ($this->config['fields'] as &$field) {
            if (empty($field['type'])) {
                $field['type'] = 'Text';
            }
        }
    }

    /**
     * @return \Ip\Grid\Model\Field[]
     */
    protected function getFieldObjects()
    {
        if ($this->fieldObjects === null) {
            $collection = array();
            foreach ($this->config['fields'] as $field) {
                if ($field['type']) {
                    $class = '\Ip\Grid\Model\Field\\' . $field['type'];
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

        switch ($method) {
            case 'init':
                return $this->refresh($data['params']);
                break;
        }

    }


    protected function getTableFields($tableName)
    {
        $sql = "SHOW COLUMNS FROM " . $this->tableName($tableName) . "";

        $fields = ipDb()->fetchColumn($sql);

        $result = array();
        foreach ($fields as $fieldName) {
            $result[] = array(
                'label' => $fieldName,
                'field' => $fieldName
            );
        }

        return $result;
    }

    protected function recordCount()
    {
        return ipDb()->fetchValue("SELECT COUNT(*) FROM " . $this->tableName($this->config['table']) . "");
    }

    protected function fetch($from, $count)
    {
        $sql = "
        SELECT
          *
        FROM
          " . $this->tableName($this->config['table']) . "
        WHERE
          1
        ORDER BY
            `id` DESC
        LIMIT
            $from, $count
        ";

        $result = ipDb()->fetchAll($sql);

        return $result;
    }

    protected function prepareData($data)
    {
        $editButtonHtml = \Ip\View::create('../view/updateButton.php');
        $deleteButtonHtml = \Ip\View::create('../view/deleteButton.php');
        $preparedData = array();
        foreach ($data as $row) {
            $preparedRow = array();
            if ($this->allowUpdate()) {
                $preparedRow[] = $editButtonHtml;
            }
            foreach ($this->getFieldObjects() as $key => $field) {
                if (isset($this->config['fields'][$key]['showInList']) && !$this->config['fields'][$key]['showInList']) {
                    continue;
                }

                $preview = $field->preview($row);
                if (!empty($this->config['fields'][$key]['filter'])) {
                    $filters = $this->config['fields'][$key]['filter'];
                    if (!is_array($filters)) {
                        $filters = array($filters);
                    }
                    foreach ($filters as $filter) {
                        if (substr($filter, 1, 1) !== '\\') {
                            $filter = '\\' . $filter;
                        }
                        $preview = call_user_func($filter, $preview, $row);
                    }
                }
                $preparedRow[] = $preview;
            }
            if ($this->allowDelete()) {
                $preparedRow[] = $deleteButtonHtml;
            }
            $preparedData[] = $preparedRow;
        }
        return $preparedData;
    }

    protected function getFieldLabels()
    {
        $labels = array();
        if ($this->allowUpdate()) {
            $labels[] = '';
        }
        foreach ($this->config['fields'] as $field) {
            if (isset($field['showInList']) && !$field['showInList']) {
                continue;
            }
            $labels[] = $field['label'];
        }
        if ($this->allowDelete()) {
            $labels[] = '';
        }
        return $labels;
    }


    protected function getActions()
    {
        $actions = array();
        if ($this->allowInsert()) {
            $actions[] = array(
                'label' => __('Add', 'ipAdmin', false),
                'class' => 'ipsAdd'
            );
        }
        if ($this->allowSearch()) {
            $actions[] = array(
                'label' => __('Search', 'ipAdmin', false),
                'class' => 'ipsSearch'
            );
        }
        if (array_key_exists('actions', $this->config) && is_array($this->config['actions'])) {
            $actions = array_merge($actions, $this->config['actions']);
        }
        return $actions;
    }


    protected function refresh($params)
    {
        $currentPage = !empty($params['page']) ? (int)$params['page'] : 1;
        if ($currentPage < 1) {
            $currentPage = 1;
        }

        $pageSize = $this->config['pageSize'];
        $from = ($currentPage - 1) * $pageSize;
        $totalPages = ceil($this->recordCount() / $pageSize);

        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $pagination = new \Ip\Pagination\Pagination(array(
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ));

        $variables = array(
            'labels' => $this->getFieldLabels(),
            'data' => $this->prepareData($this->fetch($from, $pageSize)),
            'actions' => $this->getActions(),
            'pagination' => $pagination,
        );

        $html = \Ip\View::create('../view/layout.php', $variables)->render();
        $commands = array(
            $this->commandSetHtml($html)
        );
        return $commands;
    }

    protected function tableName($tableName)
    {
        return ipTable(str_replace("`", "", $tableName));
    }


    protected function allowInsert()
    {
        return !array_key_exists('allowInsert', $this->config) || $this->config['allowInsert'];
    }

    protected function allowSearch()
    {
        return !array_key_exists('allowSearch', $this->config) || $this->config['allowSearch'];
    }

    protected function allowUpdate()
    {
        return !array_key_exists('allowUpdate', $this->config) || $this->config['allowUpdate'];
    }

    protected function allowDelete()
    {
        return !array_key_exists('allowDelete', $this->config) || $this->config['allowDelete'];
    }

}