<?php
/**
<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;


class Checkbox extends Field{

    protected $checked = null;
    protected $text = null;
    protected $postValue = null;
    
    public function __construct($options = array()) {
        if (isset($options['checked'])) {
            $this->checked = $options['checked'];
        } else {
            if(isset($options['defaultValue']) && ($options['defaultValue'] === TRUE || $options['defaultValue'] === 1)) {
                $this->checked = TRUE;
            } else {
                $this->checked = FALSE;
            }
        }
        if (isset($options['defaultValue']) && $options['defaultValue']) {
            $this->setChecked(1);
        }
        if (isset($options['text']) && $options['text']) {
            $this->setText($options['text']);
        }
        if (isset($options['postValue']) && $options['postValue']) {
            $this->setPostValue($options['postValue']);
        }

        parent::__construct($options);
    }
    
    public function render($doctype) {
        $answer = '';
        if ($this->getChecked()) {
            $attributes = 'checked="checked"';
        } else {
            $attributes = '';
        }

        if ($this->getPostValue() !== null) {
            $value = ' value="'.htmlspecialchars($this->getPostValue(), ENT_QUOTES).'" ';
        } else {
            $value = '';
        }

        $answer .= '<input '.$this->getAttributesStr($doctype).' class="ipmControlCheckbox '.implode(' ',$this->getClasses()).'" name="'.htmlspecialchars($this->getName()).'" type="checkbox" '.$attributes.'  '.$this->getValidationAttributesStr($doctype).' '.$value.' />'."\n";
        if ($this->getText()) {
            $answer .= '<div class="ipmControlConfirmOptions">'.$this->getText().'</div>';
        }
    
        return $answer; 
    }
    
    public function setChecked($checked) {
        $this->checked = $checked;
    }
    
    public function getChecked() {
        return $this->checked;
    }
    
    public function setPostValue($postValue) {
        $this->postValue = $postValue;
    }


    public function getPostValue() {
        return $this->postValue;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function getText() {
        return $this->text;
    }

    public function getValueAsString($values, $valueKey) {
        if ($this->isChecked($values, $valueKey)) {
            return 'Yes';
        } else {
            return 'No';
        }
    }

    public function isChecked($values, $valueKey) {
        if (isset($values[$valueKey]) && $values[$valueKey]) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getTypeClass() {
        return 'confirm';
    }

    public function setDefaultValue($defaultValue) {
        if ($defaultValue) {
            $this->setChecked(1);
        }
    }
    
}