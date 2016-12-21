<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model;


abstract class Field
{
    protected $field = '';
    protected $label = '';
    protected $defaultValue = '';
    protected $previewMethod = '';
    protected $attributes = [];
    protected $layout = \Ip\Form\Field::LAYOUT_DEFAULT;

    /**
     * Create field object for grid
     * @param array $fieldFieldConfig config of this particular field
     * @param $wholeConfig whole grid setup config
     * @throws \Ip\Exception
     */
    public function __construct($fieldFieldConfig, $wholeConfig)
    {
        if (!empty($fieldFieldConfig['field'])) {
            $this->field = $fieldFieldConfig['field'];
        }

        if (!empty($fieldFieldConfig['label'])) {
            $this->label = $fieldFieldConfig['label'];
        }

        if (!empty($fieldFieldConfig['layout'])) {
            $this->layout = $fieldFieldConfig['layout'];
        }

        if (!empty($fieldFieldConfig['defaultValue'])) {
            $this->defaultValue = $fieldFieldConfig['defaultValue'];
        }

        if (!empty($fieldFieldConfig['attributes'])) {
            $this->attributes = $fieldFieldConfig['attributes'];
        }

        if (!empty($fieldFieldConfig['previewMethod'])) {
            $this->previewMethod = $fieldFieldConfig['previewMethod'];
        }

    }

    /**
     * Generate field value preview for table view. HTML is allowed
     * @param $recordData
     * @internal param array $data current record data
     * @return string
     */
    public function preview($recordData)
    {
        if ($this->previewMethod) {
            return call_user_func($this->previewMethod, $recordData);
        } else {
            if (isset($recordData[$this->field])) {
                return esc($recordData[$this->field]);
            }
        }
    }

    /**
     * Return an object which can be used as a field for standard Ip\Form class.
     * @return \Ip\Form\Field
     */
    public abstract function createField();

    /**
     * Grid doesn't put user's input directly into the database. Each field type decides how to process
     * submitted data. Use this method to process submitted data and return associative array of values to be
     * stored to the database. If you need to do some other actions on other tables or process files after new
     * record has been created, use onCreate method.
     * @param array $postData user posted data
     * @return array
     */
    public abstract function createData($postData);

    /**
     * Return an object which can be used as a field for standard Ip\Form class.
     * @param array $curData current record data
     * @return \Ip\Form\Field
     */
    public abstract function updateField($curData);

    /**
     * Grid doesn't put user's input directly into the database. Each field type decides how to process
     * submitted data. Use this method to process submitted data and return associative array of values to be
     * stored to the database. If you need to do some other actions on other tables or process files after update, use onUpdate method.
     * @param array $postData user posted data
     * @return array
     */
    public abstract function updateData($postData);

    /**
     * Return an object which can be used as a field for standard Ip\Form class.
     * @param array $searchVariables current search filter values
     * @return \Ip\Form\Field
     */
    public abstract function searchField($searchVariables);

    /**
     * Process entered search values and provide part of SQL query which can be added in WHERE clause.
     * @param array $searchVariables user's posted search values
     * @return string
     */
    public abstract function searchQuery($searchVariables);

    /**
     * Executed before creating a new record
     * @param int $recordId
     * @param array $curData
     */
    public function beforeCreate($recordId, $curData)
    {
        //do nothing by default
    }

    /**
     * Executed after new record has been created
     * @param int $recordId
     * @param array $curData
     */
    public function afterCreate($recordId, $curData)
    {
        //do nothing by default
    }

    /**
     * Executed before update
     * @param int $recordId
     * @param array $oldData
     * @param array $newData
     */
    public function beforeUpdate($recordId, $oldData, $newData)
    {
        //do nothing by default
    }

    /**
     * Executed after update
     * @param int $recordId
     * @param array $oldData
     * @param array $newData
     */
    public function afterUpdate($recordId, $oldData, $newData)
    {
        //do nothing by default
    }


    /**
     * Executed before deleting the record
     * @param int $recordId
     * @param array $curData
     */
    public function beforeDelete($recordId, $curData)
    {
        //do nothing by default
    }

    /**
     * Executed after deleting the record
     * @param int $recordId
     * @param array $curData
     */
    public function afterDelete($recordId, $curData)
    {
        //do nothing by default
    }

    /**
     * @param $layout (\Ip\Form\Field::LAYOUT_DEFAULT, \Ip\Form\Field::LAYOUT_NO_LABEL, \Ip\Form\Field::BLANK)
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function getLayout()
    {
        return $this->layout;
    }


}
