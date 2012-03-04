<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Library\IpForm\Field;


class Textarea extends Field{
    
    public function render($doctype) {
        $attributesStr = '';
        
        return '<textarea name="'.htmlspecialchars($this->getName()).'" '.$this->getAttributesStr().' '.$this->getValidationAttributesStr().' >'.htmlentities($this->getDefaultValue()).'</textarea>';
    }
    
}