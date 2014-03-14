<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;


use Ip\Form\Field;

class Url extends Field
{

    public function render($doctype, $environment)
    {

        $browseButton = '';

        if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
            $browseButton = '
    <span class="input-group-btn">
        <button class="ipsBrowse btn btn-default" type="button">' . __('Browse', 'ipAdmin') . '</button>
    </span>
            ';
        }

        return '
<div class="input-group">
    <input '.$this->getAttributesStr($doctype).' class="form-control '.implode(' ',$this->getClasses()).'" name="'.htmlspecialchars($this->getName()).'" '.$this->getValidationAttributesStr($doctype).' type="text" value="'.htmlspecialchars($this->getValue()).'" />
    ' . $browseButton . '
</div>
';
    }

    /**
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getTypeClass()
    {
        return 'url';
    }

}
