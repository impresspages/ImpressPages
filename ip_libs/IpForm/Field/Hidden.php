<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Library\IpForm\Field;


class Hidden extends Field{
    
    public function render($doctype) {
        $attributesStr = '';

        return '<input name="'.htmlspecialchars($this->getName()).'" '.$this->getAttributesStr().' '.$this->getValidationAttributesStr().' type="hidden" value="'.htmlspecialchars($this->getDefaultValue()).'" />';
    }
    
    public function getLayout() {
        return self::LAYOUT_BLANK;
    }
    
    public function getType() {
        return self::TYPE_SYSTEM;
    }    
    
}