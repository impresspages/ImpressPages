<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\developer\form\Validator;



class Required extends Validator {
    
    public function validate($values, $valueKey) {
        if (empty($values[$valueKey])) {
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
