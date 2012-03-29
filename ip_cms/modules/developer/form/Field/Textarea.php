<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\developer\form\Field;


class Textarea extends Field{
    
    public function render($doctype) {
        $attributesStr = '';
        
        return '<textarea class="ipmControlTextarea" name="'.htmlspecialchars($this->getName()).'" '.$this->getAttributesStr().' '.$this->getValidationAttributesStr().' >'.htmlentities($this->getDefaultValue()).'</textarea>';
    }
    
    /**
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getCssClass() {
        return 'textarea';
    }
    
}