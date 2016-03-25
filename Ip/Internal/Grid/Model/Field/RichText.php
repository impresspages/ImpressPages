<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model\Field;


class RichText extends \Ip\Internal\Grid\Model\Field\Text
{

    public function createField()
    {
        $field = new \Ip\Form\Field\RichText(array(
            'label' => $this->label,
            'name' => $this->field,
            'layout' => $this->layout,
            'attributes' => $this->attributes
        ));
        $field->setValue($this->defaultValue);
        return $field;
    }

    public function updateField($curData)
    {
        $field = new \Ip\Form\Field\RichText(array(
            'label' => $this->label,
            'name' => $this->field,
            'layout' => $this->layout,
            'attributes' => $this->attributes
        ));
        if (isset($curData[$this->field])){
        $field->setValue($curData[$this->field]);
        }
        return $field;
    }

}
