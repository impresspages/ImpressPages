<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Modules\developer\form\Field;


class Radio extends Field{

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
        $answer = '';
        foreach($this->getValues() as $value) {
            if ($value[0]== $this->defaultValue) {
                $checked = 'checked="checked"';
            } else {
                $checked = '';
            }
            $answer .= '<label><input class="ipfControlRadio" name="'.htmlspecialchars($this->getName()).'" type="radio" '.$this->getAttributesStr().' '.$this->getValidationAttributesStr().' '.$checked.' value="'.htmlspecialchars($value[0]).'" />'.htmlspecialchars($value[1]).'</label>'."\n";
        }

        return $answer; 
    }
    
    
    public function setValues($values) {
        $this->values = $values;
    }
    
    public function getValues() {
        return $this->values;
    }
    

    
}