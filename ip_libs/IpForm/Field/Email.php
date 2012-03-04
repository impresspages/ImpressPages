<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Library\IpForm\Field;


class Email extends Field{
    
    public function render($doctype) {
        $attributesStr = '';
        
        return '<input name="'.htmlspecialchars($this->getName()).'" '.$this->getAttributesStr().' '.$this->getValidationAttributesStr().' type="email" value="'.htmlspecialchars($this->getDefaultValue()).'"/>';
    }
    

    
}