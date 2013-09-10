<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\developer\form;




class Fieldset{
    protected $fields;
    protected $label;
    
    public function __construct() {
        $this->fields = array();
    }
    
    /**
     * 
     * Add field to last fielset. Create fieldset if does not exist.
     * @param Field $field
     */
    public function addField(Field\Field  $field) {
        $this->fields[] = $field;
    }
    
    /**
     * 
     * Return all fields
     */
    public function getFields() {
        return $this->fields;
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

    public function getLabel() {
        return $this->label;
    }

    public function setLabel($label) {
        $this->label = $label;
    }

}