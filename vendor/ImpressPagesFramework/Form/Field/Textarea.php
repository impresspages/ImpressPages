<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


class Textarea extends Field
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
        return '<textarea ' . $this->getAttributesStr($doctype) . ' class="form-control ' . implode(
            ' ',
            $this->getClasses()
        ) . '" name="' . escattr($this->getName()) . '" ' . $this->getValidationAttributesStr(
            $doctype
        ) . ' >' . escTextarea($this->getValue()) . '</textarea>';
    }



}
