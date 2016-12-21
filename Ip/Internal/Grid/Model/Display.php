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



        $where = $db->buildSqlWhere();



        $pageVariableName = $subgridConfig->pageVariableName();
        $currentPage = !empty($this->statusVariables[$pageVariableName]) ? (int)$this->statusVariables[$pageVariableName] : 1;
        if ($currentPage < 1) {
            $currentPage = 1;
        }

        $pageSize = $subgridConfig->pageSize($this->statusVariables);



        $from = ($currentPage - 1) * $pageSize;

        $totalPages = ceil($db->recordCount($where) / $pageSize);

        if ($currentPage > $totalPages) {
            $totalPages = $currentPage;
        }

        $pagination = new \Ip\Pagination\Pagination(array(
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'pagerSize' => $subgridConfig->pagerSize()
        ));



        $values = array(
            10, 20, 50, 100, 1000, 10000
        );

        if (!in_array($pageSize, $values)) {
            $values[] = $pageSize;
        }

        asort($values);

        $searchVariables = [];
        foreach ($this->statusVariables as $key => $value) {
            if (preg_match('/^s_/', $key)) {
                $searchVariables[substr($key, 2)] = $value;
            }
        }


        $variables = array(
            'columns' => $this->getColumnData(),
            'data' => $this->rowsData($db->fetch($from, $pageSize, $where)),
            'actions' => $this->getActions(),
            'pagination' => $pagination,
            'deleteWarning' => $subgridConfig->deleteWarning(),
            'createForm' => $this->createForm(),
            'searchForm' => $this->searchForm($searchVariables),
            'moveForm' => $this->moveForm(),
            'title' => $subgridConfig->getTitle(),
            'breadcrumb' => $this->getBreadcrumb(),
            'pageSize' => $pageSize,
            'pageSizes' => $values
        );

        $html = ipView($subgridConfig->layout(), $variables)->render();
        return $html;
    }

    protected function getBreadcrumb()
    {
        $depth = Status::depth($this->statusVariables);

        if ($depth <= 1) {
            return [];
        }

        $breadcrumb = [];
        $gridConfig = $this->config;


        $breadcrumb[] = array(
            'title' => $gridConfig->getTitle(),
            'url' => '#'
        );

        $lastStatusVariables = [];

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

            $breadcrumbGridconfig = $gridConfig->subgridConfig($lastStatusVariables, $i);
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
        $actions = [];
        if ($this->subgridConfig->allowCreate()) {
            $actions['add'] = array(
                'label' => __('Add', 'Ip-admin', false),
                'class' => 'ipsCreate'
            );
        }
        if ($this->subgridConfig->allowSearch()) {
            $actions['search'] = array(
                'label' => __('Search', 'Ip-admin', false),
                'class' => 'ipsSearch'
            );
        }

        //select language in multilingual grid
        $values = [];
        $languages = ipContent()->getLanguages();
        foreach($languages as $language) {
            $values[] = array('label' => $language->getAbbreviation(), 'value' => $language->getCode());
        }
        $db = new Db($this->subgridConfig, $this->statusVariables);
        if ($this->subgridConfig->isMultilingual()) {
            $actions['language'] = array(
                'type' => 'Select',
                'label' => ipContent()->getLanguageByCode($db->getDefaultLanguageCode())->getAbbreviation(),
                'values' => $values,
                'itemClass' => 'ipsGridLanguageSetting'
            );
        }

        $actions = array_merge($actions, $this->subgridConfig->actions());

        $actionsFilter = $this->subgridConfig->actionsFilter();
        if ($actionsFilter) {
            $actions = $actionsFilter($actions);
        }

        //make sure the configuration is correct
        foreach ($actions as &$action) {
            if (empty($action['type'])) {
                $action['type'] = 'Button';
            }
            if (!isset($action['label'])) {
                $action['label'] = '';
            }
            if (!isset($action['values']) || !is_array($action['values'])) {
                $action['values'] = [];
            }
            foreach ($action['values'] as &$value) {
                if (!is_array($value)) {
                    $tmpVal = $value;
                    $value = [];
                    $value['value'] = $tmpVal;
                }
                if (!isset($value['value'])) {
                    $value['value'] = '';
                }
                if (!isset($value['label'])) {
                    $value['label'] = $value['value'];
                }

            }
            if ($action['type'] == 'Html' && !isset($action['html'])) {
                $action['html'] = '';
            }
            if (!isset($action['class'])) {
                $action['class'] = '';
            }
            if ($action['type'] == 'Select' && !isset($value['itemClass'])) {
                $value['itemClass'] = '';
            }

        }

        return $actions;
    }

    protected function rowsData($data)
    {
        $editButtonHtml = ipView('../view/updateButton.php');
        $deleteButtonHtml = ipView('../view/deleteButton.php');

        $disabledSorting = !empty($this->statusVariables['order']);
        $dragButtonHtml = ipView('../view/dragHandle.php', array('disabled' => $disabledSorting));
        $rows = [];
        foreach ($data as $row) {
            $preparedRow = array(
                'id' => $row[$this->subgridConfig->idField()]
            );
            $preparedRowData = [];
            if ($this->subgridConfig->allowSort()) {
                $preparedRowData[] = $dragButtonHtml;
            }

            if ($this->subgridConfig->allowUpdate()) {
                $preparedRowData[] = $editButtonHtml;
            }
            foreach ($this->subgridConfig->fields() as $fieldData) {

                if (isset($fieldData['preview']) && !$fieldData['preview'] || $fieldData['type'] == 'Tab' && empty($fieldData['preview'])) {
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
        $columns = [];
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
            if (isset($field['preview']) && !$field['preview'] || !isset($field['preview']) && $field['type'] == 'Tab') {
                continue;
            }

            $column = array(
                'label' => $field['label']
            );

            if ((!isset($field['allowOrder']) || $field['allowOrder']) && !empty($field['field'])) {
                if ($this->subgridConfig->orderField($this->statusVariables) == $field['field']) {
                    $symbol = ' ▲';
                    if ($this->subgridConfig->orderDirection($this->statusVariables) == 'desc') {
                        $symbol = '▼';
                    }
                    $column['label'] .= ' ' . $symbol;
                }
                $column ['actionAttributes'] = 'class="ipsAction _clickable" data-method="order" data-params="' . escAttr(json_encode(array('order' => $field['field']))) . '"';
            }

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
        $form->addAttribute('autocomplete', 'off');
        $curData = $db->fetchRow($id);
        $curDataMultilingual = [];
        if ($this->subgridConfig->isMultilingual()) {
            //fetch multilingual data
            $languages = ipContent()->getLanguages();
            foreach($languages as $language) {
                $langDb = new Db($this->subgridConfig, $this->statusVariables);
                $langDb->setDefaultLanguageCode($language->getCode());
                $curDataMultilingual[$language->getCode()] = $langDb->fetchRow($id);
            }

        }

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
                if (!empty($fieldData['multilingual'])) {
                    $languages = ipContent()->getLanguages();
                    foreach($languages as $language) {
                        $tmpFieldData = $fieldData;
                        $field = $this->updateField($tmpFieldData, $curDataMultilingual[$language->getCode()]);
                        $field->setName($field->getName() . '_' . $language->getCode());
                        if ($field) {
                            $field->setLabel($field->getLabel() . ' ' . $language->getAbbreviation());
                            $form->addField($field);
                        }
                    }
                } else {
                    $field = $this->updateField($fieldData, $curData);
                    if ($field) {
                        $form->addField($field);
                    }
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

        if ($this->subgridConfig->updateFormFilter()) {
            $form = call_user_func($this->subgridConfig->updateFormFilter(), $form);
        }

        return $form;
    }

    /**
     * @param $fieldData
     * @param $curData
     * @return \Ip\Form\Field
     * @throws \Ip\Exception
     */
    protected function updateField($fieldData, $curData)
    {
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


        }
        return $field;
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
                if (!empty($fieldData['multilingual'])) {
                    $languages = ipContent()->getLanguages();
                    foreach($languages as $language) {
                        $tmpFieldData = $fieldData;
                        $field = $this->createField($tmpFieldData);
                        $field->setName($field->getName() . '_' . $language->getCode());
                        if ($field) {
                            $field->setLabel($field->getLabel() . ' ' . $language->getAbbreviation());
                            $form->addField($field);
                        }
                    }
                } else {
                    $field = $this->createField($fieldData);
                    if ($field) {
                        $form->addField($field);
                    }
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


        if ($this->subgridConfig->createFormFilter()) {
            $form = call_user_func($this->subgridConfig->createFormFilter(), $form);
        }

        return $form;
    }

    protected function createField($fieldData)
    {
        $fieldObject = $this->subgridConfig->fieldObject($fieldData);

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

            return $field;
        }
        return false;
    }

    public function searchForm($searchVariables)
    {
        $form = new \Ip\Form();
        $form->setMethod('get');
        $form->addAttribute('autocomplete', 'off');
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


    public function moveForm()
    {
        $form = new \Ip\Form();

        $field = new \Ip\Form\Field\Integer(array(
                'name' => 'position',
                'value' => ''
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Hidden(array(
                'name' => 'id',
                'value' => ''
            ));
        $form->addField($field);

        return $form;
    }


}
