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

    /**
     * @var Config|null
     */
    protected $subgridConfig = null;

    public function __construct(Config $config, Config $subgridConfig, $statusVariables)
    {
        $this->config = $config;
        $this->subgridConfig = $subgridConfig;
        $this->statusVariables = $statusVariables;
    }

    public function fullHtml()
    {
        /**
         * @var Config
         */
        $subgridConfig = $this->subgridConfig;


        $db = new Db($this->subgridConfig, $this->statusVariables);

        $searchVariables = array();
        foreach ($this->statusVariables as $key => $value) {
            if (preg_match('/^s_/', $key)) {
                $searchVariables[substr($key, 2)] = $value;
            }
        }


        $where = $db->buildSqlWhere();


        if (!empty($searchVariables)) {

            foreach ($subgridConfig->fields() as $fieldData) {
                if (!empty($fieldData['type']) && $fieldData['type'] == 'Tab') {
                    continue;
                }
                $fieldObject = $subgridConfig->fieldObject($fieldData);
                $fieldQuery = $fieldObject->searchQuery($searchVariables);
                if ($fieldQuery) {
                    if ($where != ' ') {
                        $where .= ' and ';
                    }
                    $where .= $fieldQuery;
                }
            }
        }

        $pageVariableName = $subgridConfig->pageVariableName();
        $currentPage = !empty($this->statusVariables[$pageVariableName]) ? (int)$this->statusVariables[$pageVariableName] : 1;
        if ($currentPage < 1) {
            $currentPage = 1;
        }

        $pageSize = $subgridConfig->pageSize();
        $from = ($currentPage - 1) * $pageSize;

        $totalPages = ceil($db->recordCount($where) / $pageSize);

        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $pagination = new \Ip\Pagination\Pagination(array(
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'pagerSize' => $subgridConfig->pagerSize()
        ));





        $variables = array(
            'columns' => $this->getColumnData(),
            'data' => $this->rowsData($db->fetch($from, $pageSize, $where)),
            'actions' => $this->getActions(),
            'pagination' => $pagination,
            'deleteWarning' => $subgridConfig->deleteWarning(),
            'createForm' => $this->createForm(),
            'searchForm' => $this->searchForm($searchVariables),
            'title' => $subgridConfig->getTitle(),
            'breadcrumb' => $this->getBreadcrumb()
        );


        $html = ipView($subgridConfig->layout(), $variables)->render();
        return $html;
    }

    protected function getBreadcrumb()
    {
        $depth = Status::depth($this->statusVariables);

        if ($depth <= 1) {
            return array();
        }

        $breadcrumb = array();
        $gridConfig = $this->config;


        $breadcrumb[] = array(
            'title' => $gridConfig->getTitle(),
            'url' => '#'
        );

        $lastStatusVariables = array();

        $db = new Db($this->config, $this->statusVariables);



        for ($i = 1; $i <= $depth - 1; $i++) {
            if (isset($this->statusVariables['gridParentId' . ($i)])) {
                $parentId = $this->statusVariables['gridParentId' . ($i)];
            } else {
                $parentId = null;
            }
            if (isset($this->statusVariables['gridId' . ($i)])) {
                $gridId = $this->statusVariables['gridId' . ($i)];
            } else {
                $gridId = null;
            }
            $lastStatusVariables = Status::genSubgridVariables($lastStatusVariables, $gridId, $parentId);
            $tmpGridConfig = $gridConfig->subgridConfig($lastStatusVariables);
            $hash = Status::build($lastStatusVariables);

            $breadcrumbGridconfig = $gridConfig->subgridConfig($lastStatusVariables, $i - 1);
            if ($breadcrumbGridconfig->getBreadcrumbField()) {
                $title = $db->breadcrumbTitle($i);
            } else {
                $title = $tmpGridConfig->getTitle();
            }


            $breadcrumb[] = array(
                'title' => $title,
                'url' => '#' . $hash
            );
        }
        return $breadcrumb;
    }

    protected function getActions()
    {
        $actions = array();
        if ($this->subgridConfig->allowCreate()) {
            $actions[] = array(
                'label' => __('Add', 'Ip-admin', false),
                'class' => 'ipsCreate'
            );
        }
        if ($this->subgridConfig->allowSearch()) {
            $actions[] = array(
                'label' => __('Search', 'Ip-admin', false),
                'class' => 'ipsSearch'
            );
        }
        $actions = array_merge($actions, $this->subgridConfig->actions());
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
                'id' => $row[$this->subgridConfig->idField()]
            );
            $preparedRowData = array();
            if ($this->subgridConfig->allowSort()) {
                $preparedRowData[] = $dragButtonHtml;
            }

            if ($this->subgridConfig->allowUpdate()) {
                $preparedRowData[] = $editButtonHtml;
            }
            foreach ($this->subgridConfig->fields() as $fieldData) {

                if (isset($fieldData['preview']) && !$fieldData['preview']) {
                    continue;
                }

                $fieldObject = $this->subgridConfig->fieldObject($fieldData);
                $preview = $fieldObject->preview($row);

                if (!empty($fieldData['preview']) && $fieldData['preview'] !== true) {
                    if (is_callable($fieldData['preview'])) {
                        $preview = call_user_func($fieldData['preview'], $row[$fieldData['field']], $row);
                    } elseif (is_string($fieldData['preview'])) {
                        $preview = $fieldData['preview'];
                    } else {
                        throw new \Ip\Exception('Field \'preivew\' value must be PHP callable or string');
                    }

                }
                $preparedRowData[] = $preview;
            }
            if ($this->subgridConfig->allowDelete()) {
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
        if ($this->subgridConfig->allowSort()) {
            $column = array(
                'label' => ''
            );
            $columns[] = $column;
        }
        if ($this->subgridConfig->allowUpdate()) {
            $column = array(
                'label' => ''
            );
            $columns[] = $column;
        }
        foreach ($this->subgridConfig->fields() as $field) {
            if (isset($field['preview']) && !$field['preview']) {
                continue;
            }
            $column = array(
                'label' => $field['label']
            );
            $columns[] = $column;
        }
        if ($this->subgridConfig->allowDelete()) {
            $column = array(
                'label' => ''
            );
            $columns[] = $column;
        }
        return $columns;
    }


    public function updateForm($id)
    {
        $db = new Db($this->subgridConfig, $this->statusVariables);
        $form = new \Ip\Form();
        $curData = $db->fetchRow($id);
        foreach ($this->subgridConfig->fields() as $key => $fieldData) {
            if (isset($fieldData['allowUpdate']) && !$fieldData['allowUpdate']) {
                continue;
            }


            if (!empty($fieldData['type']) && $fieldData['type'] == 'Tab') {
                //tabs (fieldsets)
                $title = '';
                if (!empty($fieldData['label'])) {
                    $title = $fieldData['label'];
                }

                if ($key == 0) {
                    $fieldsets = $form->getFieldsets();
                    $fieldset = $fieldsets[0];
                    $fieldset->setLabel($title);
                } else {
                    $fieldset = new \Ip\Form\Fieldset($title);
                    $form->addFieldset($fieldset);
                }
                $fieldset->addAttribute('id', 'autoGridTabId' . rand(0, 100000000000));
                if ($key == 0) {
                    $fieldset->addAttribute('class', 'tab-pane active');
                } else {
                    $fieldset->addAttribute('class', 'tab-pane');
                }


            } else {
                //fields
                $fieldObject = $this->subgridConfig->fieldObject($fieldData);
                $field = $fieldObject->updateField($curData);
                if ($field) {
                    if (!empty($fieldData['validators'])) {
                        foreach ($fieldData['validators'] as $validator) {
                            $field->addValidator($validator);
                        }
                    }
                    if (!empty($fieldData['note'])) {
                        $field->setNote($fieldData['note']);
                    }
                    if (!empty($fieldData['hint'])) {
                        $field->setHint($fieldData['hint']);
                    }

                    $form->addField($field);
                }

            }

        }

        $field = new \Ip\Form\Field\Hidden(array(
            'name' => $this->subgridConfig->idField(),
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

        if (count($form->getFieldsets()) > 1) {
            $form->addClass('tab-content');
        }

        return $form;
    }


    public function createForm()
    {
        $form = new \Ip\Form();

        $subgridConfig = $this->subgridConfig;
        $fields = $subgridConfig->fields();




        foreach ($fields as $key => $fieldData) {
            if (isset($fieldData['allowCreate']) && !$fieldData['allowCreate']) {
                continue;
            }

            if (!empty($fieldData['type']) && $fieldData['type'] == 'Tab') {
                //tabs (fieldsets)
                $title = '';
                if (!empty($fieldData['label'])) {
                    $title = $fieldData['label'];
                }

                if ($key == 0) {
                    $fieldsets = $form->getFieldsets();
                    $fieldset = $fieldsets[0];
                    $fieldset->setLabel($title);
                } else {
                    $fieldset = new \Ip\Form\Fieldset($title);
                    $form->addFieldset($fieldset);
                }
                $fieldset->addAttribute('id', 'autoGridTabId' . rand(0, 100000000000));
                if ($key == 0) {
                    $fieldset->addAttribute('class', 'tab-pane active');
                } else {
                    $fieldset->addAttribute('class', 'tab-pane');
                }


            } else {
                //fields
                $fieldObject = $subgridConfig->fieldObject($fieldData);

                $field = $fieldObject->createField();
                if ($field) {
                    if (!empty($fieldData['validators'])) {
                        foreach ($fieldData['validators'] as $validator) {
                            $field->addValidator($validator);
                        }
                    }
                    if (!empty($fieldData['note'])) {
                        $field->setNote($fieldData['note']);
                    }
                    if (!empty($fieldData['hint'])) {
                        $field->setHint($fieldData['hint']);
                    }

                    $form->addField($field);
                }
            }
        }

        if (count($form->getFieldsets()) > 1) {
            $form->addClass('tab-content');
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
        foreach ($this->subgridConfig->fields() as $key => $fieldData) {
            if (isset($fieldData['allowSearch']) && !$fieldData['allowSearch']) {
                continue;
            }

            if (!empty($fieldData['type']) && $fieldData['type'] == 'Tab') {
                //tabs (fieldsets)
                $title = '';
                if (!empty($fieldData['label'])) {
                    $title = $fieldData['label'];
                }

                if ($key == 0) {
                    $fieldsets = $form->getFieldsets();
                    $fieldset = $fieldsets[0];
                    $fieldset->setLabel($title);
                } else {
                    $fieldset = new \Ip\Form\Fieldset($title);
                    $form->addFieldset($fieldset);
                }
                $fieldset->addAttribute('id', 'autoGridTabId' . rand(0, 100000000000));
                if ($key == 0) {
                    $fieldset->addAttribute('class', 'tab-pane active');
                } else {
                    $fieldset->addAttribute('class', 'tab-pane');
                }


            } else {

                $fieldObject = $this->subgridConfig->fieldObject($fieldData);
                $field = $fieldObject->searchField($searchVariables);
                if ($field) {
                    $form->addField($field);
                }
            }
        }


        $field = new \Ip\Form\Field\Hidden(array(
            'name' => 'method',
            'value' => 'search'
        ));
        $form->addField($field);

        $field = new \Ip\Form\Field\HiddenSubmit();
        $form->addField($field);

        if (count($form->getFieldsets()) > 1) {
            $form->addClass('tab-content');
        }

        return $form;
    }



}
