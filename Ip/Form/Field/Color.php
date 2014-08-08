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
        if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
            $confirmText = __('Confirm', 'Ip-admin');
            $cancelText = __('Cancel', 'Ip-admin');
        } else {
            $confirmText = __('Confirm', 'Ip');
            $cancelText = __('Cancel', 'Ip');
        }

        return '<input data-confirmtext=\'' . $confirmText . '\' data-canceltext=\'' . $cancelText . '\' ' . $this->getAttributesStr(
            $doctype
        ) . ' class="ipmControlInput ipsColorPicker ' . implode(
            ' ',
            $this->getClasses()
        ) . '" name="' . htmlspecialchars($this->getName()) . '" ' . $this->getValidationAttributesStr(
            $doctype
        ) . ' type="text" value="' . htmlspecialchars($this->getValue()) . '" />';
    }

    /**
     * Get class type
     *
     * CSS class that should be applied to surrounding element of this field.
     * By default empty. Extending classes should specify their value.
     * @return string
     */
    public function getTypeClass()
    {
        return 'color';
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
