<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Library\IpForm\Field;


/**
 * Empty field. Common usage is to display global form error. 
 * For example, for some reason the form could not be saved.
 * The error is not specific to any of the fields.
 * If your form could have such errors, you can put this empty 
 * field at the top of your form and assign error message to it. 
 * Then this error will appear above all fields as a global form error.
 * 
 */
class Blank extends Field{
    
    public function render($doctype) {
        return '<input style="height: 0; width: 0;" name="'.htmlspecialchars($this->getName()).'" '.$this->getAttributesStr().' '.$this->getValidationAttributesStr().' type="text" value="'.htmlspecialchars($this->getDefaultValue()).'" />';
    }
}