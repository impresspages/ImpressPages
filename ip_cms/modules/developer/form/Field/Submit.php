<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\developer\form\Field;


class Submit extends Field{
    
    public function render($doctype) {
        return '<input class="ipfControlSubmit" name="'.htmlspecialchars($this->getName()).'" '.$this->getAttributesStr().' type="submit" value="'.htmlspecialchars($this->getDefaultValue()).'" />';
    }
    
    public function getLayout() {
        return self::LAYOUT_DEFAULT;
    }
    
    
    public function getType() {
        return self::TYPE_SYSTEM;
    }
    
    /**
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getCssClass() {
        return 'submit';
    }    
    
}