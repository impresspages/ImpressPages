<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;



class Required extends Validator {

    public function validate($values, $valueKey) {
        if (!array_key_exists($valueKey, $values) || in_array($values[$valueKey], array(null, false, '', array()), true)) {
            $parametersMod = \Ip\ServiceLocator::getParametersMod();
            return $parametersMod->getValue("developer", "form", "error_messages", "required");
        } else {
            return false;
        }
    }

    public function jtoolsArgs() {
        return 'required="required"';
    }

}
