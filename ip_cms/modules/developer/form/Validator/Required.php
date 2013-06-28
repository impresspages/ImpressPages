<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
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
