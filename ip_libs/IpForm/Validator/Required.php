<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Library\IpForm\Validator;



class Required extends Validator {
    
    public function validate($value) {
        return empty($value);
    }
    
    public function jtoolsArgs() {
        return 'required="required"';
    }
    
}
