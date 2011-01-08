<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 JSC Apro media.
 * @license		GNU/GPL, see license.html
 */

namespace Modules\community\user;


if (!defined('CMS')) exit;  


/**
 *
 *
 * @package ImpressPages
 */

class Element extends \Frontend\Element {

  public function __construct($id, $zoneName){
    global $parametersMod;
    parent::__construct($id, $zoneName);

    $this->buttonTitle = $id;

    switch($id){
      case 'password_reset':
        $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
        $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
        break;
      case 'password_reset_verified':
        $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
        $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
        break;
      case 'password_reset_verification_error':
        $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
        $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
        break;
      case 'password_reset_sent_text':
        $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
        $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset');
        break;
      case 'registration_verification_required':
        $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        break;
      case 'new_email_verification_required':
        $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        break;



      case 'registration_verified':
        $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        break;
      case 'new_email_verified':
        $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_profile');
        $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_profile');
        break;
      case 'registration_verification_error':
        $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        break;
      case 'new_email_verification_error':
        $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_new_email_verification_error');
        $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_new_email_verification_error');
        break;


      case 'login':
        $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_login');
        $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_login');
        break;
      case 'profile':
        $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_profile');
        $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_profile');
        break;
      case 'registration':
        $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        break;
      case 'renewed_registration':
        $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        break;
      case 'renew_registration_error':
        $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        break;
      case 'verification_error_user_exist':
        $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        break;
      case 'verification_error_email_exist':
        $this->buttonTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        $this->pageTitle = $parametersMod->getValue('community', 'user', 'translations', 'title_registration');
        break;
    }

    $this->zoneName = $zoneName;
  }


  public function getLink() {
    global $site;
    return $site->generateUrl(null, $this->zoneName, array($this->id));
  }

  public function getDepth() {
    return 1;
  }



  public function generateContent() {
    global $site;
    global $session;
    global $parametersMod;

    $answer = '';

    $user = $site->getZone($this->zoneName);

    switch($this->getId()) {
      case 'password_reset':
        if($session->loggedIn()) {
          if($parametersMod->getValue('community', 'user', 'options', 'zone_after_login'))
            $answer .= '<script type="text/javascript">document.location = \''.$site->generateUrl(null, $parametersMod->getValue('community', 'user', 'options', 'zone_after_login')).'\';</script>';
          else
            $answer .= '<script type="text/javascript">document.location = \''.$site->generateUrl(null, $this->zoneName, array('profile')).'\';</script>';
        }else {
          if($parametersMod->getValue('community', 'user', 'options', 'allow_password_reset')) {
            $answer .= Template::passwordReset($user->generatePasswordReset());
          }else
            $answer = '';
        }
        break;
      case 'password_reset_sent_text':
        if($parametersMod->getValue('community', 'user', 'options', 'allow_password_reset')) {
          $answer .= Template::passwordResetSentText();
        }
        break;
      case 'password_reset_verified':
        if($parametersMod->getValue('community', 'user', 'options', 'allow_password_reset')) {
          if($session->loggedIn()) {
            if($parametersMod->getValue('community', 'user', 'options', 'zone_after_login'))
              $answer .= '<script type="text/javascript">document.location = \''.$site->generateUrl(null, $parametersMod->getValue('community', 'user', 'options', 'zone_after_login')).'\';</script>';
            else
              $answer .= '<script type="text/javascript">document.location = \''.$site->generateUrl(null, $this->zoneName, array('profile')).'\';</script>';
          } else {
            $answer .= Template::passwordResetVerified($user->generateLogin());
          }
        }
        break;

      case 'password_reset_verification_error':
        if($parametersMod->getValue('community', 'user', 'options', 'allow_password_reset')) {
          $answer .= Template::passwordResetVerificationError();
        }
        break;

      case 'registration_verification_required':
        $answer .= Template::registrationVerificationRequired();
        break;

      case 'new_email_verification_required':
        $answer .= Template::newEmailVerificationRequired();
        break;

      case 'registration_verified':
      case 'new_email_verified':
        if($session->loggedIn()) {
          $answer .= '
            <script type="text/javascript">document.location = \''.$site->generateUrl(null, $this->zoneName, array('profile')).'\';</script>
            ';
        }else {
          $answer .= Template::registrationVerified($user->generateLogin());
        }
        break;
      case 'registration_verification_error':
        $answer .= Template::registrationVerificationError();
        break;
      case 'verification_error_user_exist':
        $answer .= Template::verificationErrorUserExist();
        break;
      case 'verification_error_email_exist':
        $answer .= Template::verificationErrorEmailExist();
        break;
      case 'new_email_verification_error':
        $answer .= Template::newEmailVerificationError();
        break;
      case 'login':
        if($session->loggedIn()) {
          if($parametersMod->getValue('community', 'user', 'options', 'zone_after_login'))
            $answer .= '<script type="text/javascript">document.location = \''.$site->generateUrl(null, $parametersMod->getValue('community', 'user', 'options', 'zone_after_login')).'\';</script>';
          else
            $answer .= '<script type="text/javascript">document.location = \''.$site->generateUrl(null, $this->zoneName, array('profile')).'\';</script>';
        }else {
          if($parametersMod->getValue('community','user','options','allow_password_reset'))
            $resetLink = $user->getLinkPasswordReset();
          else
            $resetLink = '';

          if($parametersMod->getValue('community','user','options','registration_on_login_page') && $parametersMod->getValue('community','user','options','enable_registration'))
            $registrationLink = $user->getLinkRegistration();
          else
            $registrationLink = '';

          $answer .= Template::login($user->generateLogin(), $resetLink, $registrationLink);
        }
        break;
      case 'profile':
        $answer .= Template::profile($user->generateProfile(), isset($_REQUEST['message']) && $_REQUEST['message'] == 'updated');
        break;
      case 'registration':
        if($parametersMod->getValue('community','user','options','enable_registration')) {
          $answer .= Template::registration($user->generateRegistration());
        }else {
          $answer .= Template::registrationDisabledError();
        }
        break;
      case 'renewed_registration':
        $answer .= Template::renewedRegistration();
        break;
      case 'renew_registration_error':
        $answer .= Template::renewRegistrationError();
        break;
    }


    return $answer;
  }


  public function generateManagement() {
    return $this->generateContent();
  }

}




