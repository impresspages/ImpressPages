<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Modules\developer\form\Field;


class Textarea extends Field{
    
    public function render($doctype) {
        $attributesStr = '';
        
        return '<textarea class="ipfControlTextarea" name="'.htmlspecialchars($this->getName()).'" '.$this->getAttributesStr().' '.$this->getValidationAttributesStr().' >'.htmlentities($this->getDefaultValue()).'</textarea>';
    }
    
}