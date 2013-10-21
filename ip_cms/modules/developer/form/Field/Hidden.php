<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\developer\form\Field;


class Hidden extends Field{
    
    public function render($doctype) {
        $attributesStr = '';

        return '<input '.$this->getAttributesStr($doctype).' class="'.implode(' ',$this->getClasses()).'" name="'.htmlspecialchars($this->getName()).'" '.$this->getValidationAttributesStr($doctype).' type="hidden" value="'.htmlspecialchars($this->getDefaultValue()).'" />';
    }
    
    public function getLayout() {
        return self::LAYOUT_BLANK;
    }
    
    public function getType() {
        return self::TYPE_SYSTEM;
    }    
    
    /**
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getTypeClass() {
        return 'hidden';
    }    
    
}