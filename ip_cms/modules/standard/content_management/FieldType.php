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
     * @param string $jsOptionsFunction
     * @param string $jsSaveOptionsFunction
     * @param array $jsData
     */
    public function __construct($key, $fieldClass, $title, $jsOptionsFunction = null, $jsSaveOptionsFunction = null, $jsData = array()) {
        $this->key = $key;
        $this->fieldClass = $fieldClass;
        $this->title = $title;
        $this->jsOptionsFunction = $jsOptionsFunction;
        $this->jsSaveOptionsFunction = $jsSaveOptionsFunction;
        $this->jsData = $jsData;
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
    public function getJsOptionsFunction() {
        return $this->jsOptionsFunction;
    }
    public function getJsSaveOptionsFunction() {
        return $this->jsSaveOptionsFunction;
    }
    public function getJsData() {
        return $this->jsData;
    }
}