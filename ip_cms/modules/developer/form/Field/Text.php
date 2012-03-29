<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\developer\form\Field;


class Text extends Field{
    
    public function render($doctype) {
        $attributesStr = '';

        return '<input class="ipmControlInput" name="'.htmlspecialchars($this->getName()).'" '.$this->getAttributesStr($doctype).' '.$this->getValidationAttributesStr($doctype).' type="text" value="'.htmlspecialchars($this->getDefaultValue()).'" />';
    }
    
    /**
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getCssClass() {
        return 'text';
    }
    
}