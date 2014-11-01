<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


class Number extends Field
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
        return '<input ' . $this->getAttributesStr($doctype) . ' class="form-control ' . implode(
            ' ',
            $this->getClasses()
        ) . '" name="' . htmlspecialchars($this->getName()) . '" ' . $this->getValidationAttributesStr(
            $doctype
        ) . ' type="number" value="' . htmlspecialchars($this->getValue()) . '" />';
    }



}
