<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Modules\developer\form\Field;


class Password extends Field{
    
    public function render($doctype) {
        $attributesStr = '';

        return '<input class="ipfControlInput" name="'.htmlspecialchars($this->getName()).'" '.$this->getAttributesStr().' '.$this->getValidationAttributesStr().' type="password" value="'.htmlspecialchars($this->getDefaultValue()).'" />';
    }
    

    
}