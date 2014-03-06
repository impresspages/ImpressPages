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
        $db = new Db($this->config);

        $searchVariables = array();
        foreach ($statusVariables as $key => $value) {
            if (preg_match('/^s_/', $key)) {
                $searchVariables[substr($key, 2)] = $value;
            }
        }


        if (empty($searchVariables)) {
            $where = $this->config->filter();
        } else {
            $where = $this->config->filter();
            foreach ($this->config->fields() as $fieldData) {
                $fieldObject = $this->config->fieldObject($fieldData);
                $fieldQuery = $fieldObject->searchQuery($searchVariables);
                if ($fieldQuery) {
                    if ($where != ' ') {
                        $where .= ' and ';
                    }
                    $where .= $fieldQuery;
                }
            }
        }




        $pageVariableName = $this->config->pageVariableName();
        $currentPage = !empty($statusVariables[$pageVariableName]) ? (int)$statusVariables[$pageVariableName] : 1;
        if ($currentPage < 1) {
            $currentPage = 1;
        }

        $pageSize = $this->config->pageSize();
        $from = ($currentPage - 1) * $pageSize;

        $totalPages = ceil($db->recordCount($where) / $pageSize);

        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $pagination = new \Ip\Pagination\Pagination(array(
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ));

        $variables = array(
            'columns' => $this->getColumnData(),
            'data' => $this->rowsData($db->fetch($from, $pageSize, $where)),
            'actions' => $this->getActions(),
            'pagination' => $pagination,
            'deleteWarning' => $this->config->deleteWarning(),
            'createForm' => $this->createForm(),
            'searchForm' => $this->searchForm($searchVariables),
            'title' => $this->config->getTitle()
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

                if (isset($fieldData['preview']) && !$fieldData['preview']) {
                    continue;
                }

                $fieldObject = $this->config->fieldObject($fieldData);
                $preview = $fieldObject->preview($row);

                if (!empty($fieldData['preview']) && is_callable($fieldData['preview'], TRUE)) {
                    $preview = call_user_func($fieldData['preview'], $row[$fieldData['field']], $row);
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
            if (isset($field['preview']) && !$field['preview']) {
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
        $db = new Db($this->config);
        $form = new \Ip\Form();
        $curData = $db->fetchRow($id);
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

    public function searchForm($searchVariables)
    {
        $form = new \Ip\Form();
        $form->setMethod('get');

        $form->removeCsrfCheck();
        foreach ($this->config->fields() as $fieldData) {
            $fieldObject = $this->config->fieldObject($fieldData);
            $field = $fieldObject->searchField($searchVariables);
            if ($field) {
                $form->addField($field);
            }
        }


        $field = new \Ip\Form\Field\Hidden(array(
            'name' => 'method',
            'value' => 'search'
        ));
        $form->addField($field);

        $field = new \Ip\Form\Field\HiddenSubmit();
        $form->addField($field);

        return $form;
    }


}
