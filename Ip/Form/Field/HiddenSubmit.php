<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;


use Ip\Form\Field;

class HiddenSubmit extends Field
{

    public function render($doctype, $environment) {
        // element should be available for browsers but we remove it from the regular flow
        return '<input type="submit" style="position: absolute; left: -999999px; width: 1px; height: 1px; visibility: hidden;" tabindex="-1" '.$this->getAttributesStr($doctype).' class="'.implode(' ',$this->getClasses()).'" name="'.htmlspecialchars($this->getName()).'" '.$this->getValidationAttributesStr($doctype).' value="'.htmlspecialchars($this->getValue()).'" />';
    }

    public function getLayout() {
        return self::LAYOUT_BLANK;
    }

}
