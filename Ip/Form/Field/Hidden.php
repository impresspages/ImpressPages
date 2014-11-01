<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


class Hidden extends Field
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
        ) . '" name="' . htmlspecialchars($this->getName()) . '" ' . $this->getValidationAttributesStr(
            $doctype
        ) . ' type="hidden" value="' . htmlspecialchars($this->getValue()) . '" />';
    }

    /**
     * Get layout
     *
     * @return string
     */
    public function getLayout()
    {
        return self::LAYOUT_BLANK;
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
