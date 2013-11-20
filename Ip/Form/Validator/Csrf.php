<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;


/**  
 * 
 * 'Check' antispam field validator
 * @author Mangirdas
 *
 */

class Csrf extends Validator {
    
    public function validate($values, $valueKey) {
        if (empty($values[$valueKey])) {
            return 'error';
        }

        $session = \Ip\ServiceLocator::application();

        if ($values[$valueKey] != $session->getSecurityToken()) {
            $parametersMod = \Ip\ServiceLocator::getParametersMod();
            return $parametersMod->getValue("Form.xss");
        } else {
            return false;
        }

    }
    
}