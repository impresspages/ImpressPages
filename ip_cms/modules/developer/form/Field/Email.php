<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\developer\form\Field;


class Email extends Field{
    
    public function __construct($options) {
        parent::__construct($options);
        $this->addValidator('Email');
    }
    
    public function render($doctype) {
        $attributesStr = '';

        return '<input '.$this->getAttributesStr($doctype).' class="ipmControlInput '.implode(' ',$this->getClasses()).'" name="'.htmlspecialchars($this->getName()).'" '.$this->getValidationAttributesStr($doctype).' type="email" value="'.htmlspecialchars($this->getDefaultValue()).'" />';
    }
    
    /**
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getTypeClass() {
        return 'email';
    }
    
}