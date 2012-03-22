<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Library\IpForm\Field;


class Select extends Field{

    private $values;
    
    public function __construct($options = array()) {
        if (isset($options['values'])) {
            $this->values = $options['values'];
        } else {
            $this->values = array();
        }
        parent::__construct($options);
    }
    
    public function render($doctype) {
        $attributesStr = '';
        $options = '';
        foreach($this->getValues() as $value) {
            if ($value[0]== $this->defaultValue) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            $options .= '<option name="'.htmlspecialchars($this->getName()).'" '.$this->getAttributesStr().' '.$this->getValidationAttributesStr().' '.$selected.' value="'.htmlspecialchars($value[0]).'">'.htmlspecialchars($value[1]).'</option>'."\n";
        }
$answer = 
'
<select class="ipfControlSelect" '.$this->getAttributesStr().' '.$this->getValidationAttributesStr().' >
'.$options.'
</select>
';
        return $answer; 
    }
    
    public function setValues($values) {
        $this->values = $values;
    }
    
    public function getValues() {
        return $this->values;
    }
    
    

    
}



