<?php
/**
 * @package ImpressPages
 *
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
        
        foreach($this->getValues() as $key => $value) {
            if ($value[0]== $this->defaultValue) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            
            $options .= '<option '.$selected.' value="'.htmlspecialchars($value[0]).'">'.htmlspecialchars($value[1]).'</option>'."\n";
        }
$answer = 
'
<select '.$this->getAttributesStr($doctype).' id="'.$this->stolenId.'" name="'.htmlspecialchars($this->getName()).'" class="ipmControlSelect '.implode(' ',$this->getClasses()).'" '.$this->getValidationAttributesStr($doctype).' >
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
    public function getTypeClass() {
        return 'select';
    }
    
    public function getId() {
        return $this->stolenId;
    }    

    /**
     * HTML5 spec: The first child option element of a select element with a required attribute and without a multiple attribute, and whose size is 1, must have either an empty value attribute, or must have no text content.
     * @see Modules\developer\form\Field.Field::getValidationAttributesStr()
     */
    public function getValidationAttributesStr($doctype) {
        $attributesStr = '';
        $values = $this->getValues();
        if (!isset($values[0])) {
            return parent::getValidationAttributesStr($doctype);
        }
        
        $firstValue = $values[0];
        
        $html5Important = ($doctype == \Ip\View::DOCTYPE_HTML5 && $this->getAttribute('size') <= 1 && $this->getAttribute('multiple') === false && ($firstValue[0] != '' && $firstValue[1] != ''));

        if (!$html5Important) {
            return parent::getValidationAttributesStr($doctype);
        }
        
        foreach($this->getValidators() as $validator) {
            if (get_class($validator) == 'Modules\developer\form\Validator\Required') {
                continue;
            }
            $tmpArgs = $validator->jtoolsArgs();
            if ($tmpArgs != '') {
                $attributesStr .= ' '.$tmpArgs;
            }
        }
        return $attributesStr;
    }
    
    
}



