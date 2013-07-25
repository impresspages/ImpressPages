<?php
/**
 * @package ImpressPages

 *
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
     * @return \Modules\developer\form\Field\Field
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
            'name' => $fieldData['name'],
            'required' => $fieldData['required'],
            'options' => $fieldData['options']
        );
        
        $fieldClass = $this->getFieldClass();
        
        if (isset($fieldData['options'])) {
            switch($fieldClass) {
                case '\Modules\developer\form\Field\Select':
                case '\Modules\developer\form\Field\Radio':
                    $selectValues = array();
                    if (isset($fieldData['options']['list']) && is_array($fieldData['options']['list'])) {
                        foreach($fieldData['options']['list'] as $option) {
                            if (is_string($option)) {
                                $selectValues[] = array($option, $option);
                            }
                            
                        }
                    }
                    $fieldOptions['values'] = $selectValues;
                    break;
                case '\Modules\developer\form\Field\Confirm':
                    if (isset($fieldData['options']['text']) && is_string($fieldData['options']['text'])) {
                        $fieldOptions['text'] = $fieldData['options']['text'];
                    }
                    break;
                default:
                    //other classes do not have their options. If you write your custom field type, extend this class and change this behaviour
                    break;
            }
        }
        
        if (!class_exists($fieldClass)) {
            throw new Exception('Required field type class doesn\'t exist. '.$fieldClass, Exception::UNKNOWN);
        }
        $field = new $fieldClass($fieldOptions);
        
        if (isset($fieldData['required']) && $fieldData['required']) {
            $field->addValidator('Required');
        }
        
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