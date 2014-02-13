<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model;

/**
 * Table helper class designated to prepare data for display
 * @package Ip\Internal\Grid\Model
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
            'columns' => $this->getColumnData(),
            'data' => $this->rowsData($this->fetch($from, $pageSize)),
            'actions' => $this->getActions(),
            'pagination' => $pagination,
            'deleteWarning' => $this->config->deleteWarning(),
            'createForm' => $this->createForm()
        );

        $html = ipView('../view/layout.php', $variables)->render();
        return $html;
    }

    protected function getActions()
    {
        $actions = array();
        if ($this->config->allowCreate()) {
            $actions[] = array(
                'label' => __('Add', 'ipAdmin', false),
                'class' => 'ipsCreate'
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
        $editButtonHtml = ipView('../view/updateButton.php');
        $deleteButtonHtml = ipView('../view/deleteButton.php');
        $dragButtonHtml = ipView('../view/dragHandle.php');
        $rows = array();
        foreach ($data as $row) {
            $preparedRow = array(
                'id' => $row[$this->config->idField()]
            );
            $preparedRowData = array();
            if ($this->config->allowSort()) {
                $preparedRowData[] = $dragButtonHtml;
            }

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



    protected function recordCount()
    {
        return ipDb()->fetchValue("SELECT COUNT(*) FROM " . $this->config->tableName() . "");
    }

    protected function fetch($from, $count)
    {
        if ($this->config->sortField()) {
            $sortField = $this->config->sortField();
        } else {
            $sortField = 'id';
        }

        $sql = "
        SELECT
          *
        FROM
          " . $this->config->tableName() . "
        WHERE
          1
        ORDER BY
            `" . $sortField . "` DESC
        LIMIT
            $from, $count
        ";

        $result = ipDb()->fetchAll($sql);

        return $result;
    }


    protected function fetchRow($id)
    {
        $sql = "
        SELECT
          *
        FROM
          " . $this->config->tableName() . "
        WHERE
          `" . $this->config->idField() . "` = :id
        ";

        $params = array(
            'id' => $id
        );

        $result = ipDb()->fetchRow($sql, $params);

        return $result;
    }



    protected function getColumnData()
    {
        $columns = array();
        if ($this->config->allowSort()) {
            $column = array(
                'label' => ''
            );
            $columns[] = $column;
        }
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


    public function updateForm($id)
    {
        $form = new \Ip\Form();
        $curData = $this->fetchRow($id);
        foreach ($this->config->fields() as $fieldData) {
            $fieldObject = $this->config->fieldObject($fieldData);
            $field = $fieldObject->updateField($curData);
            if ($field) {
                if (!empty($fieldData['validators'])) {
                    foreach($fieldData['validators'] as $validator) {
                        $field->addValidator($validator);
                    }
                }
                $form->addField($field);
            }
        }

        $field = new \Ip\Form\Field\Hidden(array(
            'name' => 'recordId',
            'value' => $id
        ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Hidden(array(
            'name' => 'method',
            'value' => 'update'
        ));
        $form->addField($field);

        $field = new \Ip\Form\Field\HiddenSubmit();
        $form->addField($field);

        return $form;
    }


    public function createForm()
    {
        $form = new \Ip\Form();
        foreach ($this->config->fields() as $fieldData) {
            $fieldObject = $this->config->fieldObject($fieldData);
            $field = $fieldObject->createField();
            if ($field) {
                if (!empty($fieldData['validators'])) {
                    foreach($fieldData['validators'] as $validator) {
                        $field->addValidator($validator);
                    }
                }
                $form->addField($field);
            }
        }


        $field = new \Ip\Form\Field\Hidden(array(
            'name' => 'method',
            'value' => 'create'
        ));
        $form->addField($field);

        $field = new \Ip\Form\Field\HiddenSubmit();
        $form->addField($field);

        return $form;
    }


}
