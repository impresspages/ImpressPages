<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


class Submit extends Field
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
        return '<button ' . $this->getAttributesStr($doctype) . ' class="btn btn-default ' . implode(
            ' ',
            $this->getClasses()
        ) . '" name="' . htmlspecialchars($this->getName()) . '" ' . $this->getValidationAttributesStr(
            $doctype
        ) . ' type="submit">' . htmlspecialchars($this->getValue()) . '</button>';
    }



    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE_SYSTEM;
    }

}
