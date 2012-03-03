<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;


class FieldType{
    
    protected $key;
    protected $fieldClass;
    protected $title;
    protected $jsOptionsFunction;
    protected $jsSaveOptionsFunction;
    protected $jsData;
    
    /**
     * 
     * $jsOptionsFunction and $jsSaveOptionsFunction should both present or both be skipped.
     * @param string $fieldClass
     * @param string $title
     * @param string $jsOptionsInitFunction
     * @param string $jsOptionsSaveFunction
     * @param array $jsOptionsHtml
     */
    public function __construct($key, $fieldClass, $title, $jsOptionsInitFunction = null, $jsOptionsSaveFunction = null, $jsOptionsHtml = null) {
        $this->key = $key;
        $this->fieldClass = $fieldClass;
        $this->title = $title;
        $this->jsOptionsInitFunction = $jsOptionsInitFunction;
        $this->jsOptionsSaveFunction = $jsOptionsSaveFunction;
        $this->jsOptionsHtml = $jsOptionsHtml;
    }
    
    /**
     * 
     * Create field that could be used in form class.
     * @param array $options will be passed to field constructor
     * @return \Library\IpForm\Field\Field
     */
    public function createField($options) {
        if (!$options || !is_array($options)) {
            $options = array();
        }
        $fieldClass = $this->getFieldClass();
        $field = new $fieldClass($options);
        return $field;
        
    }
    
    public function getKey() {
        return $this->key;
    }
    
    public function getFieldClass() {
        return $this->fieldClass;
    }
    public function getTitle() {
        return $this->title;
    }
    public function getJsOptionsInitFunction() {
        return $this->jsOptionsInitFunction;
    }
    public function getJsOptionsSaveFunction() {
        return $this->jsOptionsSaveFunction;
    }
    public function getJsOptionsHtml() {
        return $this->jsOptionsHtml;
    }
}