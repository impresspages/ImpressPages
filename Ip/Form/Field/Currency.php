<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


class Currency extends Field
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
        return '<input type="number" min="0.00" step="0.01" ' . $this->getAttributesStr($doctype) . ' class="form-control ' . implode(
            ' ',
            $this->getClasses()
        ) . '" name="' . htmlspecialchars($this->getName()) . '" ' . $this->getValidationAttributesStr(
            $doctype
        ) . ' value="' . htmlspecialchars($this->getValue()) . '" />';
    }


    /**
     * Check if field passes validation
     *
     * @param string $values
     * @param string $valueKey
     * @param string $environment \Ip\Form::ENVIRONMENT_ADMIN or \Ip\Form::ENVIRONMENT_PUBLIC
     * @return bool
     */
    public function validate($values, $valueKey, $environment)
    {
        if (!empty($values[$valueKey]) && !preg_match('/^[0-9]+(?:\.[0-9]{0,2})?$/', $values[$valueKey])) {
            if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                return __('Please enter correct currency format. Eg. 10.50', 'Ip-admin', false);
            } else {
                return __('Please enter correct currency format. Eg. 10.50', 'Ip', false);
            }
        }

        return parent::validate($values, $valueKey, $environment);
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
        return 'currency';
    }

}
