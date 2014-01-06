<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;


use Ip\Form\Field;

class HiddenSubmit extends Field{

    public function render($doctype) {
        //TODOXX add CSS to make it hidden #127
        return '<input type="submit" '.$this->getAttributesStr($doctype).' class="ipmHiddenSubmit '.implode(' ',$this->getClasses()).'" name="'.htmlspecialchars($this->getName()).'" '.$this->getValidationAttributesStr($doctype).' type="hidden" value="'.htmlspecialchars($this->getValue()).'" />';
    }

    public function getLayout() {
        return self::LAYOUT_BLANK;
    }

}
