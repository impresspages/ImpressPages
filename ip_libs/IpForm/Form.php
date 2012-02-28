<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2012 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Library\IpForm;



class Form{
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';
    
    protected $pages;
    protected $method;
    protected $action;
    protected $attributes;
    
    public function __construct() {
        $this->fieldsets = array();
        $this->method = self::METHOD_POST;
        $this->action = '';
        $this->pages = array();
        $this->attributes = array();
    }
    
    public function addPage(Page $page) {
        $this->pages[] = $page;
    }
    
    public function addFieldset(Fieldset $fieldset) {
        if (count($this->pages) == 0) {
            $this->addPage(new Page());
        }
        end($this->pages)->addFieldset($fieldset);
    }
    
    /**
     * 
     * Add field to last fielset. Create fieldset if does not exist.
     * @param Field $field
     */
    public function addField(Field\Field $field) {
        if (count($this->pages) == 0) {
            $this->addPage(new Page());
        }
        end($this->pages)->addField($field);
    }
    
    /**
     * 
     * Return all pages
     */
    public function getPages() {
        return $this->pages;
    }
    
    /**
     * 
     * Set post method.  
     * @param string $method Use \Library\IpForm\Form::METHOD_POST or \Library\IpForm\Form::METHOD_GET
     * @throws Exception
     */
    public function setMethod($method) {
        switch($method) {
            case self::METHOD_POST:
            case self::METHOD_GET:
                $this->method = $method;
                break;
            default:
                throw new Exception ('Unknown method "'.$method.'"', Exception::INCORRECT_METHOD_TYPE);
        }
    }
    
    public function getMethod() {
        return $this->method;
    }
    
    public function setAction($action) {
        $this->action = $action;
    }
    
    public function getAction() {
        return $this->action;
    }
    
    /**
     * 
     * Add attribute to the form
     * @param stsring $name
     * @param string $value
     */
    public function addAttribute($name, $value) {
        $this->attributes[$name] = $value;
    }
    
    public function getAttributes() {
        return $this->attributes;
    }
    
    public function getAttributesStr() {
        $answer = '';
        foreach ($this->getAttributes() as $attributeKey => $attributeValue) {
            $answer .= ' '.htmlspecialchars($attributeKey).'="'.htmlspecialchars($attributeValue).'"';
        }
        return $answer;
    }

    public function render(\Ip\View $view = null) {
        if (!$view) {
            $view = \Ip\View::create('view/form.php');
        }
        $view->setData(array('form' => $this)); 
        return $view->render();
    }
    
    public function __toString() {
        $this->render();
    }
    
}