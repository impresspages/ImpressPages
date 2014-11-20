<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model\Field;

class Info extends \Ip\Internal\Grid\Model\Field\Text
{

    public function createField()
    {
        $field = new \Ip\Form\Field\Info(array(
            'label' => $this->label,
            'name' => $this->field,
            'html' => $this->html
        ));
        return $field;
    }

    public function updateField($curData)
    {
        $field = new \Ip\Form\Field\Info(array(
            'label' => $this->label,
            'name' => $this->field,
            'html' => $this->html
        ));
        return $field;
    }

}
