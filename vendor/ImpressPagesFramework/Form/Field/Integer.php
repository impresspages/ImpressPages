<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


class Integer extends Field
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
        return '<input type="number" step="1" ' . $this->getAttributesStr($doctype) . ' class="form-control ' . implode(
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
        if (!empty($values[$valueKey]) && !preg_match('/^[-+]?[1-9]\d*$/', $values[$valueKey])) {
            if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                return __('Integer required', 'Ip-admin', false);
            } else {
                return __('Integer required', 'Ip', false);
            }
        }

        return parent::validate($values, $valueKey, $environment);
    }




}
