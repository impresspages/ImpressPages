<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\developer\form\Field;


class Textarea extends Field{
    
    public function render($doctype) {
        $attributesStr = '';
        
        return '<textarea '.$this->getAttributesStr($doctype).' class="ipmControlTextarea '.implode(' ',$this->getClasses()).'" name="'.htmlspecialchars($this->getName()).'" '.$this->getValidationAttributesStr($doctype).' >'.htmlentities($this->getDefaultValue()).'</textarea>';
    }
    
    /**
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getTypeClass() {
        return 'textarea';
    }
    
}