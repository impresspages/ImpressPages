<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;

use Ip\Form\Validator;


/**
 *
 * 'Check' antispam field validator
 *
 */

class Csrf extends Validator {

    public function getError($values, $valueKey, $environment) {
        if (empty($values[$valueKey])) {
            return 'error';
        }

        $session = \Ip\ServiceLocator::application();

        if ($values[$valueKey] != $session->getSecurityToken()) {
            if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                $errorText = __('Session has expired. Please refresh the page.', 'Ip-admin');
            } else {
                $errorText = __('Session has expired. Please refresh the page.', 'Ip');
            }
            $errorText;
        } else {
            return false;
        }

    }

}
