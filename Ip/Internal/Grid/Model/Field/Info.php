<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model\Field;

class Info extends \Ip\Internal\Grid\Model\Field
{

    protected $html = '';


    /**
     * Create field object for grid
     * @param array $fieldFieldConfig config of this particular field
     * @param $wholeConfig whole grid setup config
     * @throws \Ip\Exception
     */
    public function __construct($fieldFieldConfig, $wholeConfig)
    {
        if (!empty($fieldFieldConfig['html'])) {
            $this->html = $fieldFieldConfig['html'];
        }
        return parent::__construct($fieldFieldConfig, $wholeConfig);
    }

    public function createField()
    {
        $field = new \Ip\Form\Field\Info(array(
            'label' => $this->label,
            'name' => $this->field,
            'html' => $this->html,
            'layout' => $this->layout,
            'attributes' => $this->attributes
        ));
        return $field;
    }

    public function updateField($curData)
    {
        $field = new \Ip\Form\Field\Info(array(
            'label' => $this->label,
            'name' => $this->field,
            'html' => $this->html,
            'layout' => $this->layout,
            'attributes' => $this->attributes
        ));
        return $field;
    }


    public function setHtml($html)
    {
        $this->html = $html;
    }

    public function getHtml()
    {
        return $this->html;
    }



    /**
     * Return an object which can be used as a field for standard Ip\Form class.
     * @param array $searchVariables current search filter values
     * @return \Ip\Form\Field
     */
    public function searchField($searchVariables)
    {
        $field = new \Ip\Form\Field\Info(array(
            'label' => $this->label,
            'name' => $this->field,
            'html' => $this->html,
            'layout' => $this->layout,
            'attributes' => $this->attributes
        ));
        return $field;
    }

    /**
     * Grid doesn't put user's input directly into the database. Each field type decides how to process
     * submitted data. Use this method to process submitted data and return associative array of values to be
     * stored to the database. If you need to do some other actions on other tables or process files after new
     * record has been created, use onCreate method.
     * @param array $postData user posted data
     * @return array
     */
    public function createData($postData) {
        return [];
    }

    /**
     * Grid doesn't put user's input directly into the database. Each field type decides how to process
     * submitted data. Use this method to process submitted data and return associative array of values to be
     * stored to the database. If you need to do some other actions on other tables or process files after update, use onUpdate method.
     * @param array $postData user posted data
     * @return array
     */
    public function updateData($postData) {
        return [];
    }


    /**
     * Process entered search values and provide part of SQL query which can be added in WHERE clause.
     * @param array $searchVariables user's posted search values
     * @return string
     */
    public function searchQuery($searchVariables) {
        return false;
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
            return '';
        }
    }

}
