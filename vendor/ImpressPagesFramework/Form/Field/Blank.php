<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


/**
 * Empty field. Common usage is to display global form error.
 * For example, for some reason the form could not be saved.
 * The error is not specific to any of the fields.
 * If your form could have such errors, you can put this empty
 * field at the top of your form and assign error message to it.
 * Then this error will appear above all fields as a global form error.
 */
class Blank extends Field
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
        return '<input ' . $this->getAttributesStr($doctype) . ' class="' . implode(
            ' ',
            $this->getClasses()
        ) . '" name="' . htmlspecialchars($this->getName()) . '"  ' . $this->getValidationAttributesStr(
            $doctype
        ) . ' type="hidden" value="' . htmlspecialchars($this->getValue()) . '" />';
    }

}
