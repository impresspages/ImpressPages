<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;


use Ip\Form\Field;

class RichText extends Field{

    public function render($doctype, $environment) {
        return '<textarea ' . $this->getAttributesStr($doctype) . ' class="form-control ' . implode(' ', $this->getClasses()) . '" name="' . esc($this->getName(), 'attr') . '" ' . $this->getValidationAttributesStr($doctype) . ' >' . esc($this->getValue(), 'textarea') . '</textarea>';
    }

    /**
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getTypeClass() {
        return 'richtext';
    }

}
