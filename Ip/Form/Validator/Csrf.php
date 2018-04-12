<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;

use Ip\Form\Validator;


/**
 * Check antispam field validator
 */
class Csrf extends Validator
{

    /**
     * Get error
     *
     * @param array $values
     * @param int $valueKey
     * @param $environment
     * @return string|bool
     */
    public function getError($values, $valueKey, $environment)
    {
        if (empty($values[$valueKey])) {
            return 'error';
        }

        $session = \Ip\ServiceLocator::application();
        $secToken = $session->getSecurityToken();

        // remove the token from $_SESSION, so it is one use only
        unset( $_SESSION['ipSecurityToken'] );

        if ($values[$valueKey] != $secToken) {
            if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                $errorText = __('Session has expired. Please refresh the page.', 'Ip-admin');
            } else {
                $errorText = __('Session has expired. Please refresh the page.', 'Ip');
            }

            return $errorText;
        } else {
            return false;
        }
    }

}
