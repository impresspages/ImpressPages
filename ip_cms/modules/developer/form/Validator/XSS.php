<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\developer\form\Validator;


/**  
 * 
 * 'Check' antispam field validator
 * @author Mangirdas
 *
 */

class XSS extends Validator {
    
    public function validate($values, $valueKey) {
        if (empty($values[$valueKey])) {
            return 'error';
        }

        $session = \Ip\ServiceLocator::getSession();

        if ($values[$valueKey] != $session->getSecurityToken()) {
            $parametersMod = \Ip\ServiceLocator::getParametersMod();
            return $parametersMod->getValue("developer", "form", "error_messages", "xss");
        } else {
            return false;
        }

    }
    
}