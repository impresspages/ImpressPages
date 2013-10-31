<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\User;



class Event extends \Ip\Event{

    const LOGIN = 'ipUser.login';
    const LOGOUT = 'ipUser.logout';
    const REGISTRATION = 'ipUser.registration';
    const REGISTRATION_VERIFICATION = 'ipUser.registrationVerification';
    const PROFILE_UPDATE = 'ipUser.profileUpdate';
    const NEW_EMAIL_VERIFICATION = 'ipUser.newEmailVerification';
    const PASSWORD_RESET = 'ipUser.passwordReset';
    const PASSWORD_RESET_VERIFICATION = 'ipUser.passwordResetVerification';
    const RENEW_REGISTRATION = 'ipUser.renewRegistration';

    public function __construct($object, $name, $values) {
        parent::__construct($object, $name, $values);
    }
}