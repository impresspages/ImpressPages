<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid\Model;

/**
 * Table helper class designated to prepare data for display
 * @package Ip\Grid\Model
 */
class Display
{

    /**
     * @var Config
     */
    protected $config = null;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function fullHtml($statusVariables)
    {
        $currentPage = !empty($statusVariables['page']) ? (int)$statusVariables['page'] : 1;
        if ($currentPage < 1) {
            $currentPage = 1;
        }

        $pageSize = $this->config->pageSize();
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
            'columns' => $this->getColumnDate(),
            'data' => $this->rowsData($this->fetch($from, $pageSize)),
            'actions' => $this->getActions(),
            'pagination' => $pagination
        );

        $html = \Ip\View::create('../view/layout.php', $variables)->render();
        return $html;
    }

    protected function getActions()
    {
        $actions = array();
        if ($this->config->allowInsert()) {
            $actions[] = array(
                'label' => __('Add', 'ipAdmin', false),
                'class' => 'ipsAdd'
            );
        }
        if ($this->config->allowSearch()) {
            $actions[] = array(
                'label' => __('Search', 'ipAdmin', false),
                'class' => 'ipsSearch'
            );
        }
        $actions = array_merge($actions, $this->config->actions());
        return $actions;
    }

    protected function rowsData($data)
    {
        $editButtonHtml = \Ip\View::create('../view/updateButton.php');
        $deleteButtonHtml = \Ip\View::create('../view/deleteButton.php');
        $rows = array();
        foreach ($data as $row) {
            $preparedRow = array(
                'id' => $row[$this->config->idField()]
            );
            $preparedRowData = array();
            if ($this->config->allowUpdate()) {
                $preparedRowData[] = $editButtonHtml;
            }
            foreach ($this->config->fields() as $fieldData) {

                if (isset($fieldData['showInList']) && !$fieldData['showInList']) {
                    continue;
                }

                $fieldObject = $this->config->fieldObject($fieldData);
                $preview = $fieldObject->preview($row);
                if (!empty($fieldData['filter'])) {
                    $filters = $fieldData['filter'];
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
                $preparedRowData[] = $preview;
            }
            if ($this->config->allowDelete()) {
                $preparedRowData[] = $deleteButtonHtml;
            }

            $preparedRow['values'] = $preparedRowData;
            $rows[] = $preparedRow;
        }
        return $rows;
    }

    protected function getTableFields($tableName)
    {
        $sql = "SHOW COLUMNS FROM " . $this->config->tableName() . "";

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
        return ipDb()->fetchValue("SELECT COUNT(*) FROM " . $this->config->tableName() . "");
    }

    protected function fetch($from, $count)
    {
        $sql = "
        SELECT
          *
        FROM
          " . $this->config->tableName() . "
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



    protected function getColumnDate()
    {
        $columns = array();
        if ($this->config->allowUpdate()) {
            $column = array(
                'label' => ''
            );
            $columns[] = $column;
        }
        foreach ($this->config->fields() as $field) {
            if (isset($field['showInList']) && !$field['showInList']) {
                continue;
            }
            $column = array(
                'label' => $field['label']
            );
            $columns[] = $column;
        }
        if ($this->config->allowDelete()) {
            $column = array(
                'label' => ''
            );
            $columns[] = $column;
        }
        return $columns;
    }





}