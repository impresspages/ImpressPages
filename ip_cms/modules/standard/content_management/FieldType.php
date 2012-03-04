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
    protected $jsOptionsHtml;
    
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
     * @param array $fieldData will be passed to field constructor
     * @return \Library\IpForm\Field\Field
     */
    public function createField($fieldData) {
        if (!isset($fieldData['label'])) {
            $fieldData['label'] = '';
        }
        if (!isset($fieldData['name'])) {
            throw new \Exception('Field name not specified');
        }
        
        $fieldOptions = array(
            'label' =>$fieldData['label'],
            'name' => $fieldData['name']
        );
        
        $fieldClass = $this->getFieldClass();
        
        if (isset($fieldData['options'])) {
            switch($fieldClass) {
                case '\Library\IpForm\Field\Select':
                case '\Library\IpForm\Field\Radio':
                    $selectValues = array();
                    if (is_array($fieldData['options'])) {
                        foreach($fieldData['options'] as $option) {
                            $selectValues[] = array($option, $option);
                        }
                    }
                    $fieldOptions['values'] = $selectValues;
                    break;
                default:
                    //other classes do not have their options. If you write your custom field type, extend this class and change this behaviour
                    break;
            }
        }
        
        $field = new $fieldClass($fieldOptions);
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