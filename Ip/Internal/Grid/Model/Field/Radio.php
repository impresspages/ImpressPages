<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model\Field;


class Radio extends \Ip\Internal\Grid\Model\Field
{
    protected $field = '';
    protected $label = '';
    protected $defaultValue = '';
    protected $values = [];

    public function __construct($fieldFieldConfig, $wholeConfig)
    {
        if (empty($fieldFieldConfig['field'])) {
            throw new \Ip\Exception('\'field\' option required for text field');
        }
        $this->field = $fieldFieldConfig['field'];

        if (!empty($fieldFieldConfig['label'])) {
            $this->label = $fieldFieldConfig['label'];
        }

        if (!empty($fieldFieldConfig['values'])) {
            $this->values = $fieldFieldConfig['values'];
        }

        if (!empty($fieldFieldConfig['defaultValue'])) {
            $this->defaultValue = $fieldFieldConfig['defaultValue'];
        }
    }

    public function preview($recordData)
    {
        $previewValue = $recordData[$this->field];
        foreach ($this->values as $value) {
            if (is_array($value) && isset($value[1]) && $value[0] == $previewValue) {
                $previewValue = $value[1];
                break;
            }
        }
        return esc($previewValue);
    }

    public function createField()
    {
        $field = new \Ip\Form\Field\Radio(array(
            'label' => $this->label,
            'name' => $this->field,
            'values' => $this->values,
            'layout' => $this->layout,
            'attributes' => $this->attributes
        ));
        $field->setValue($this->defaultValue);
        return $field;
    }

    public function createData($postData)
    {
        if (isset($postData[$this->field])) {
            return array($this->field => $postData[$this->field]);
        }
        return [];
    }

    public function updateField($curData)
    {
        $field = new \Ip\Form\Field\Radio(array(
            'label' => $this->label,
            'name' => $this->field,
            'values' => $this->values,
            'layout' => $this->layout,
            'attributes' => $this->attributes
        ));
        if (isset($curData[$this->field])){
        $field->setValue($curData[$this->field]);
        }
        return $field;
    }

    public function updateData($postData)
    {
        return array($this->field => $postData[$this->field]);
    }


    public function searchField($searchVariables)
    {
        $values = array(array(null, 'Any'));
        $values = array_merge($values, $this->values);

        $field = new \Ip\Form\Field\Radio(array(
            'label' => $this->label,
            'name' => $this->field,
            'values' => $values,
            'layout' => $this->layout,
            'attributes' => $this->attributes
        ));
        if (!empty($searchVariables[$this->field])) {
            $field->setValue($searchVariables[$this->field]);
        }
        return $field;
    }

    public function searchQuery($searchVariables)
    {
        if (isset($searchVariables[$this->field]) && $searchVariables[$this->field] !== '') {
            return '`' . $this->field . '` = ' . ipDb()->getConnection()->quote($searchVariables[$this->field]) . ' ';
        }
        return null;
    }
}
