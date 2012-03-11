<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Library\IpForm\Field;


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
        if (!empty($options['attributes'])) {
            $this->setAttributes($options['attributes']);
        } else {
            $this->setAttributes(array());
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
    
    public function getAttributesStr() {
        $answer = '';
        foreach ($this->getAttributes() as $attributeKey => $attributeValue) {
            $answer .= ' '.htmlspecialchars($attributeKey).'="'.htmlspecialchars($attributeValue).'"';
        }
        return $answer;
    }
    
    public function getValueAsString($postedValue) {
        return (string) $postedValue;
    }
    
    
    public function getValidators() {
        return $this->validators;
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
            throw new \Library\IpForm\Exception("Unknown validator: '".$validator."'", \Library\IpForm\Exception::UNKNOWN_VALIDATOR);
        }
        $validatorClass = '\\Library\\IpForm\\Validator\\' . $validator;
        eval ('$validator = new '.$validatorClass.'();');
        
        $this->validators[] = $validator;
        
    }
    
    public function addCustomValidator(\Library\IpForm\Validator $validator) {
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
    
    public function getValidationAttributesStr() {
        $attributesStr = '';
        foreach($this->getValidators() as $validator) {
            $tmpArgs = $validator->jtoolsArgs();
            if ($tmpArgs != '') {
                $attributesStr .= ' '.$tmpArgs;
            }
        }
        return $attributesStr;
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
    
    public function getAttributes() {
        return $this->attributes;
    }
    
    public function setAttributes($attributes) {
        $this->attributes = $attributes;
    }
}