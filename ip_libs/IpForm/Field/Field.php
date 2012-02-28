<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Library\IpForm\Field;


abstract class Field{

    const LAYOUT_DEFAULT = 'default';
    const LAYOUT_BLANK = 'blank';
    
    protected $label;
    protected $note;
    protected $hint;
    protected $name;
    protected $defaultValue;
    protected $validators;
    
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
        if (!empty($options['defaultValue'])) {
            $this->setDefaultValue($options['defaultValue']);
        }
    }
    
    public abstract function render($doctype);
    
    public function __toString() {
        return $this->render(DEFAULT_DOCTYPE);
    }
    
    public function getLayout() {
        return self::LAYOUT_DEFAULT;
    }
    
    public function getValidators() {
        return $this->validators;
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

    public function getDefaultValue() {
        return $this->defaultValue;
    }
    
    public function setDefaultValue($defaultValue) {
        $this->defaultValue = $defaultValue;
    }
    
}