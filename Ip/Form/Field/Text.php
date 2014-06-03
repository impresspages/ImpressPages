<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


class Text extends Field
{

    /**
     * Render field
     *
     * @param string $doctype
     * @param $environment
     * @return string
     */
    public function render($doctype, $environment) {
        return '<input ' . $this->getAttributesStr($doctype) . ' class="form-control ' . implode(' ', $this->getClasses()) . '" name="' . htmlspecialchars($this->getName()) . '" ' . $this->getValidationAttributesStr($doctype) . ' type="text" value="' . htmlspecialchars($this->getValue()) . '" />';
    }

    /**
     * Get class type
     *
     * CSS class that should be applied to surrounding element of this field.
     * By default empty. Extending classes should specify their value.
     * @return string
     */
    public function getTypeClass() {
        return 'text';
    }

}
