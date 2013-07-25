<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\developer\form\Validator;



class Required extends Validator {
    
    public function validate($values, $valueKey) {
        if (empty($values[$valueKey])) {
            return 'required';
        } else {
            return false;
        }
    }
    
    public function jtoolsArgs() {
        return 'required="required"';
    }
    
}
