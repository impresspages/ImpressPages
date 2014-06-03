<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


class Email extends Field
{

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        $this->addValidator('Email');
    }

    /**
     * Render field
     *
     * @param string $doctype
     * @param $environment
     * @return string
     */
    public function render($doctype, $environment) {
        $attributesStr = '';

        return '<input ' . $this->getAttributesStr($doctype) . ' class="form-control ' . implode(' ', $this->getClasses()) . '" name="' . htmlspecialchars($this->getName()) . '" ' . $this->getValidationAttributesStr($doctype) . ' type="email" value="' . htmlspecialchars($this->getValue()) . '" />';
    }

    /**
     * Get class type
     *
     * CSS class that should be applied to surrounding element of this field.
     * By default empty. Extending classes should specify their value.
     * @return string
     */
    public function getTypeClass() {
        return 'email';
    }

}
