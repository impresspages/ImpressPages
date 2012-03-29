<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\developer\form\Field;


class Select extends Field{

    private $values;
    private $stolenId;
    
    
    public function __construct($options = array()) {
        if (isset($options['values'])) {
            $this->values = $options['values'];
        } else {
            $this->values = array();
        }
        parent::__construct($options);
        $this->stolenId = $this->getAttribute('id');
        $this->removeAttribute('id'); //we need to put id only on the first input. So we will remove it from attributes list. And put it temporary to stolenId;
        
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
            $options .= '<option '.$this->getAttributesStr().' '.$this->getValidationAttributesStr().' '.$selected.' value="'.htmlspecialchars($value[0]).'">'.htmlspecialchars($value[1]).'</option>'."\n";
        }
$answer = 
'
<select name="'.htmlspecialchars($this->getName()).'" class="ipmControlSelect" '.$this->getAttributesStr().' '.$this->getValidationAttributesStr().' >
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
    
    
    /**
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getCssClass() {
        return 'select';
    }
    
    public function getId() {
        return $this->stolenId;
    }    
    
}



