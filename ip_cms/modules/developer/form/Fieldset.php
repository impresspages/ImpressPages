<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Modules\developer\form;




class Fieldset{
    protected $fields;
    
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
    

}