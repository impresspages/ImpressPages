<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;


use Ip\Form\Field;

class Text extends Field{

    public function render($doctype, $environment) {
        return '<input '.$this->getAttributesStr($doctype).' class="form-control '.implode(' ',$this->getClasses()).'" name="'.htmlspecialchars($this->getName()).'" '.$this->getValidationAttributesStr($doctype).' type="text" value="'.htmlspecialchars($this->getValue()).'" />';
    }

    /**
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getTypeClass() {
        return 'text';
    }

}
