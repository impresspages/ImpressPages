<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;


use Ip\Form\Field;

class Hidden extends Field{
    
    public function render($doctype) {
        return '<input '.$this->getAttributesStr($doctype).' class="'.implode(' ',$this->getClasses()).'" name="'.htmlspecialchars($this->getName()).'" '.$this->getValidationAttributesStr($doctype).' type="hidden" value="'.htmlspecialchars($this->getValue()).'" />';
    }

    public function getLayout() {
        return self::LAYOUT_BLANK;
    }

    public function getType() {
        return self::TYPE_SYSTEM;
    }

    public function getTypeClass() {
        return 'hidden';
    }
}
