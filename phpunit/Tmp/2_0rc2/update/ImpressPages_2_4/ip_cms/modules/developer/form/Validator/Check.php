<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\developer\form\Validator;


/**  
 * 
 * 'Check' antispam field validator
 * @author Mangirdas
 *
 */

class Check extends Validator {
    
    public function validate($values, $valueKey) {
        if (empty($values[$valueKey])) {
            return 'error';
        }
        $value = $values[$valueKey];
        
        if (!is_array($value) || count($value) != 2) {
            return 'error';
        }
        
        //first value should stay empty. Or its a bot :O)
        if (!isset($value[0]) || $value[0] != '') {
            return 'error';
        }
        
        //second value should be encoded today or yesterday date. Yesterday date is needed if user started to fill in data at 23:59 
        if (!isset($value[1]) || ($value[1] != md5(date("Y-m-d").SESSION_NAME) && $value[1] != date('Y-m-d', time() - 24*60*60))) {
            return 'error';
        }
        
        return false;
    }
    
}