<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Library\IpForm;



class Page{
    protected $fieldsets;
    
    public function __construct() {
        $this->fieldsets = array();
    }
    
    public function addFieldset($fieldset) {
        $this->fieldsets[] = $fieldset;
    }
    
    /**
     * 
     * Add field to last fielset. Create fieldset if does not exist.
     * @param Field $field
     */
    public function addField(Field\Field  $field) {
        if (count($this->fieldsets) == 0) {
            $this->addFieldset(new Fieldset());
        }
        end($this->fieldsets)->addField($field);
    }
    
    /**
     * 
     * Return all fieldset
     */
    public function getFieldsets() {
        return $this->fieldsets;
    }
    

}