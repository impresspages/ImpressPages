<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


class Url extends Field
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
        $browseButton = '';

        if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
            $browseButton = '
    <span class="input-group-btn">
        <button class="ipsBrowse btn btn-default" type="button">' . __('Browse', 'Ip-admin') . '</button>
    </span>
            ';
        }

        return '
<div class="input-group">
    <input ' . $this->getAttributesStr($doctype) . ' class="form-control ' . implode(
            ' ',
            $this->getClasses()
        ) . '" name="' . htmlspecialchars($this->getName()) . '" ' . $this->getValidationAttributesStr(
            $doctype
        ) . ' type="text" value="' . htmlspecialchars($this->getValue()) . '" />
    ' . $browseButton . '
</div>
';
    }


}
