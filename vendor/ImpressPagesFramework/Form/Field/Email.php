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
    public function __construct($options = array())
    {
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
    public function render($doctype, $environment)
    {
        return '<input ' . $this->getAttributesStr($doctype) . ' class="form-control ' . implode(
            ' ',
            $this->getClasses()
        ) . '" name="' . htmlspecialchars($this->getName()) . '" ' . $this->getValidationAttributesStr(
            $doctype
        ) . ' type="email" value="' . htmlspecialchars($this->getValue()) . '" />';
    }


}
