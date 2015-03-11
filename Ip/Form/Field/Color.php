<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


class Color extends Field
{

    /**
     * Render field
     *
     * @param string $doctype
     * @param $environment
     * @return string
     */
    public function render($doctype, $environment)
    {
        return '
<div class="input-group">
    <span class="input-group-addon"><i></i></span>
    <input ' . $this->getAttributesStr($doctype)
        . ' class="form-control ipsColorPicker '
        . implode(' ', $this->getClasses())
        . '" name="' . htmlspecialchars($this->getName()) . '" '
        . $this->getValidationAttributesStr($doctype)
        . ' type="text" value="' . htmlspecialchars($this->getValue()) . '" />
</div>
        ';

    }



    /**
     * Validate input value
     *
     * @param array $values all values of the form
     * @param string $valueKey key of value in values array that needs to be validated
     * @param \Ip\Form $environment
     * @return bool|string return string on error or false on success
     */
    public function validate($values, $valueKey, $environment)
    {
        if (preg_match('/^#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$\b/', $values[$valueKey])) {
            return parent::validate($values, $valueKey, $environment);
        } else {
            if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                return __('Incorrect color code', 'Ip-admin', false);
            } else {
                return __('Incorrect color code', 'Ip', false);
            }
        }
    }

}
