<?php
/**
<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Library\IpForm\Field;


class Radio extends Field{

    private $checked;
    
    public function __construct($options = array()) {
        if (isset($options['checked'])) {
            $this->checked = $options['checked'];
        } else {
            if($this->defaultValue === TRUE) {
                $this->checked = TRUE;
            } else {
                $this->checked = FALSE;
            }
        }
        parent::__construct($options);
    }
    
    public function render($doctype) {
        $attributesStr = '';
        $answer = '';
        if ($this->getChecked()) {
            $attributes = 'checked="checked"';
        } else {
            $attributes = '';
        }
        $answer .= '<radio '.$attributes.' '.$this->getValidationAttributesStr().' value="'.htmlspecialchars($value[0]).'"/>'."/n";
        
        $answer .= '<span>'.htmlspecialchars($value[1]).'</span>'."/n";
    
        return $answer; 
    }
    
    public function setChecked($checked) {
        $this->checked = $checked;
    }
    
    public function getChecked() {
        return $this->checked;
    }
    
}