<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\developer\form\Field;


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
        return '<input '.$this->getAttributesStr($doctype).' class="ipmControlBlank '.implode(' ',$this->getClasses()).'" name="'.htmlspecialchars($this->getName()).'"  '.$this->getValidationAttributesStr($doctype).' type="text" value="'.htmlspecialchars($this->getDefaultValue()).'" />';
    }
    
    /**
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getTypeClass() {
        return 'blank';
    }    
}