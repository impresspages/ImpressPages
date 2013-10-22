<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\developer\form\Field;


class Radio extends Field{

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
        $answer = '';
        foreach($this->getValues() as $key => $value) {
            if ($value[0]== $this->defaultValue) {
                $checked = 'checked="checked"';
            } else {
                $checked = '';
            }
            if ($key == 0) {
                $id = 'id="'.$this->stolenId.'"';
            } else {
                $id = '';
            }
            
            $answer .= '<label><input '.$this->getAttributesStr($doctype).' '.$id.' class="ipmControlRadio '.implode(' ',$this->getClasses()).'" name="'.htmlspecialchars($this->getName()).'" type="radio" '.$this->getValidationAttributesStr($doctype).' '.$checked.' value="'.htmlspecialchars($value[0]).'" />'.htmlspecialchars($value[1]).'</label>'."\n";
        }

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
    public function getTypeClass() {
        return 'radio';
    }
    
    public function getId() {
        return $this->stolenId;
    }    
}