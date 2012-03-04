<?php
/**
<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Library\IpForm\Field;


class Checkbox extends Field{

    protected $checked;
    protected $text;
    
    public function __construct($options = array()) {
        if (isset($options['checked'])) {
            $this->checked = $options['checked'];
        } else {
            if(isset($options['defaultValue']) && $options['defaultValue'] === TRUE) {
                $this->checked = TRUE;
            } else {
                $this->checked = FALSE;
            }
        }
        if (isset($options['text'])) {
            $this->setText($options['text']);
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
        $answer .= '<input type="checkbox" '.$attributes.' '.$this->getValidationAttributesStr().' value="'.htmlspecialchars($this->getDefaultValue()).'"/>'."\n";
        if ($this->getText()) {
            $answer .= '<div>'.$this->getText().'</div>';
        }
    
        return $answer; 
    }
    
    public function setChecked($checked) {
        $this->checked = $checked;
    }
    
    public function getChecked() {
        return $this->checked;
    }
    
    public function setText($text) {
        $this->text = $text;
    }
    
    public function getText() {
        return $this->text;
    }
    
    
    
}