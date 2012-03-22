<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Modules\developer\form\Validator;



class Required extends Validator {
    
    public function validate($values, $valueKey) {
        return empty($values[$valueKey]);
    }
    
    public function jtoolsArgs() {
        return 'required="required"';
    }
    
}
