<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;


class Color extends Field{
    
    public function render($doctype) {
        $parametersMod = \Ip\ServiceLocator::getParametersMod();
        return '<input data-confirmtext=\''.htmlspecialchars($parametersMod->getValue('standard', 'configuration', 'admin_translations', 'confirm')).'\' data-canceltext=\''.htmlspecialchars($parametersMod->getValue('standard', 'configuration', 'admin_translations', 'cancel')).'\' '.$this->getAttributesStr($doctype).' class="ipmControlInput ipsColorPicker '.implode(' ',$this->getClasses()).'" name="'.htmlspecialchars($this->getName()).'" '.$this->getValidationAttributesStr($doctype).' type="text" value="'.htmlspecialchars($this->getDefaultValue()).'" />';
    }

    /**
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getTypeClass() {
        return 'color';
    }

    /**
     * Validate field
     * @param array $data usually array of string. But some elements could be null or even array (eg. password confirmation field, or multiple file upload field)
     * @param string $valueKey This value key could not exist in values array.
     * @return string return string on error or false on success
     */
    public function validate($values, $valueKey)
    {
        if (preg_match('/^#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$\b/', $values[$valueKey])) {
            return false;
        } else {
            return 'Incorrect color code';
        }
    }

}