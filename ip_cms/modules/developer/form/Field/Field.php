<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\developer\form\Field;


abstract class Field{
    //layouts define how field should be treatet in the view
    const LAYOUT_DEFAULT = 'default';
    const LAYOUT_BLANK = 'blank';
    
    //types define how field values should be used in controller. Eg. 'system' fields
    //should not be sent by email as form post data. They are just helpers to deliver
    //form to the controller (eg. hidden fields, submit button, captcha).
    const TYPE_REGULAR = 'regular';
    const TYPE_SYSTEM = 'system'; 
    
    protected $label;
    protected $note;
    protected $hint;
    protected $name;
    protected $dbField; //where in db this value should be stored by the method writeToDatabase
    protected $defaultValue;
    protected $validators;
    protected $attributes;
    protected $classes; // CSS classes to be added to input field
    
    public function __construct($options) {
        $this->validators = array();
        
        if (!empty($options['validators'])) {
            foreach($options['validators'] as $validatorKey => $validator) {
                $this->addValidator($validator);
            }
        }
        
        if (!empty($options['label'])) {
            $this->setLabel($options['label']);
        }
        if (!empty($options['note'])) {
            $this->setNote($options['note']);
        }
        if (!empty($options['hint'])) {
            $this->setHint($options['hint']);
        }
        if (!empty($options['name'])) {
            $this->setName($options['name']);
        }
        if (!empty($options['dbField'])) {
            $this->setDbField($options['dbField']);
        }
        if (!empty($options['defaultValue'])) {
            $this->setDefaultValue($options['defaultValue']);
        }
        if (!empty($options['css'])) {
            if (is_array($options['css'])) {
                $this->setCssClasses($options['css']);
            } else {
                $this->classes = array($options['css']);
            }
        } else {
            $this->classes = array();
        }
        if (!empty($options['attributes'])) {
            $this->setAttributes($options['attributes']);
        } else {
            $this->setAttributes(array());
        }
        if (!isset($this->attributes['id'])) {
            $this->addAttribute('id', 'field_'.rand(1, PHP_INT_MAX));
        }
        
        
    }
    
    public abstract function render($doctype);
    
    public function __toString() {
        return $this->render(DEFAULT_DOCTYPE);
    }
    
    public function getLayout() {
        return self::LAYOUT_DEFAULT;
    }
    
    public function getType() {
        return self::TYPE_REGULAR;
    }
    
    public function getAttributesStr($doctype) {
        $answer = '';
        foreach ($this->getAttributes() as $attributeKey => $attributeValue) {
            $answer .= ' '.htmlspecialchars($attributeKey).'="'.htmlspecialchars($attributeValue).'"';
        }
        return $answer;
    }
    
    /**
     * @param array $values all posted form values
     * @param string $valueKey this field name
     */
    public function getValueAsString($values, $valueKey) {
        if (isset($values[$valueKey])) {
            return $values[$valueKey];
        } else {
            return '';
        }
    }
    
    
    public function getValidators() {
        return $this->validators;
    }
    
    public function isRequired() {
        $validators = $this->getValidators();
        foreach($validators as $validator) {
            if (get_class($validator) == 'Modules\developer\form\Validator\Required') {
                return true;
            }
        }
        return false;
    }
    
    /**
     * 
     * Validate if field passes validation
     * 
     */
    /**
     * Validate field
     * @param array $data usually array of string. But some elements could be null or even array (eg. password confirmation field, or multiple file upload field)
     * @param string $valueKey This value key could not exist in values array.
     * @return string return string on error or false on success
     */
    public function validate($values, $valueKey) {
        $validators = $this->getValidators();
        foreach($validators as $validator) {
            $error = $validator->validate($values, $valueKey);
            if ($error) {
                return $error;
            }
        }
        return false;
    }
    
    /**
     * Add validator to field
     * @param string $validator
     */
    public function addValidator($validator) {
        if (!preg_match('/^[a-z0-9]+$/i', $validator)) {
            throw new \Modules\developer\form\Exception("Unknown validator: '".$validator."'", \Modules\developer\form\Exception::UNKNOWN_VALIDATOR);
        }
        $validatorClass = '\\Modules\\developer\\form\\Validator\\' . $validator;
        eval ('$validator = new '.$validatorClass.'();');
        
        $this->validators[] = $validator;
        
    }
    
    public function removeValidator($validator) {
        $validatorClass = 'Modules\\developer\\form\\Validator\\' . $validator;
        $newValidatorsArray = array();
        foreach($this->validators as $validator) {
            if (get_class($validator) != $validatorClass) {
                $newValidatorsArray[] = $validator;
            }
        }
        $this->validators = $newValidatorsArray;
    }
    
    public function addCustomValidator(\Modules\developer\form\Validator\Validator $validator) {
        $this->validators[] = $validator;
    }
    
    /**
     * 
     * Adds attribute to input field. Altenative way to setAttributes method.
     * @param string $name
     * @param string $value
     */
    public function addAttribute($name, $value) {
        $this->attributes[$name] = $value;
    }
    
    public function removeAttribute($name) {
        unset($this->attributes[$name]);
    }
    
    public function getValidationAttributesStr($doctype) {
        $attributesStr = '';
        foreach($this->getValidators() as $validator) {
            $tmpArgs = $validator->jtoolsArgs();
            if ($tmpArgs != '') {
                $attributesStr .= ' '.$tmpArgs;
            }
        }
        return $attributesStr;
    }
    
    /**
     * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their constant value.
     * This field is not used to identify fields by their type. So each extending class should return its own unique and constant string.
     */
    public function getTypeClass() {
        return '';
    }
    
    
    /* GETTERS AND SETTERS  */
    
    public function getLabel() {
        return $this->label;
    }
    
    public function setLabel($label) {
        $this->label = $label;
    }

    public function getHint() {
        return $this->hint;
    }
    
    public function setHint($hint) {
        $this->hint = $hint;
    }

    public function getNote() {
        return $this->note;
    }
    
    public function setNote($note) {
        $this->note = $note;
    }

    public function getName() {
        return $this->name;
    }

    /**
     * If your input has many input fields. Eg. field[id], field[code], ... Return the name of input that should hold error message
     * @return string
     */
    public function getValidationInputName(){
        return $this->name;
    }
    
    public function setName($name) {
        $this->name = $name;
    }

    public function getDbField() {
        return $this->dbField;
    }
    
    public function setDbField($dbField) {
        $this->dbField = $dbField;
    }
    
    public function getDefaultValue() {
        return $this->defaultValue;
    }
    
    public function setDefaultValue($defaultValue) {
        $this->defaultValue = $defaultValue;
    }
    
    public function getAttribute($attribute) {
        if (isset($this->attributes[$attribute])) {
            return $this->attributes[$attribute];
        } else {
            return false;
        }
    }
    
    public function getAttributes() {
        return $this->attributes;
    }
    
    public function setAttributes($attributes) {
        $this->attributes = $attributes;
    }
    
    public function getId() {
        return $this->getAttribute('id');
    }
    
    
    /**
    *
    * Add CSS class to the input
    * @param string $cssClass
    */
    public function addClass($cssClass) {
        $this->classes[$cssClass] = 1;
    }
    
    public function removeClass($cssClass) {
        unset($this->classes[$cssClass]);
    }
    
    public function getClasses() {
        return array_keys($this->classes);
    }
    
    public function getClassesStr() {
        $answer = '';
        foreach ($this->getClasses() as $class) {
            $answer .= ' '.$class;
        }
        return 'class="'.$answer.'"';
    }    
}