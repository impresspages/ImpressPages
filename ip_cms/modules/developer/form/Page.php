<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\developer\form;




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
    
    
    /**
     * Return all fields (from all fieldsets in one level array)
     */
    public function getFields() {
        $fieldsets = $this->getFieldsets();
        $fields = array();
        foreach ($fieldsets as $fieldset) {
            $fields = array_merge($fields, $fieldset->getFields());
        }
        return $fields;
    }    
    
    public function getField($name) {
        $allFields = $this->getFields();
        foreach($allFields as $key => $field) {
            if ($field->getName() == $name) {
                return $field;
            }
        }
        return false;
    }    
    

}