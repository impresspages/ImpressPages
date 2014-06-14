<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


class Farbtastic extends Field
{

    /**
     * Render field
     *
     * @param string $doctype
     * @param $environment
     * @return string
     */
    public function render($doctype, $environment) {
        return '<div class="ipmFarbtasticPopup ipsFarbtasticPopup"></div>
        <input ' . $this->getAttributesStr($doctype) . ' class="form-control ipsControlInput ' . implode(' ', $this->getClasses()) . '" name="' . htmlspecialchars($this->getName()) . '" ' . $this->getValidationAttributesStr($doctype) . ' type="text" value="' . htmlspecialchars($this->getValue()) . '" />';
    }

    /**
     * Get class type
     *
     * CSS class that should be applied to surrounding element of this field.
     * By default empty. Extending classes should specify their value.
     * @return string
     */
    public function getTypeClass() {
        return 'farbtastic';
    }

    /**
     * Validate field
     *
     * @param array $values usually array of string. But some elements could be null or even array (eg. password confirmation field, or multiple file upload field).
     * @param string $valueKey This value key could not exist in values array.
     * @return bool|string Return string on error or false on success.
     */
    public function validate($values, $valueKey) {
        return false;
    }

}
