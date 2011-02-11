<?php
/**
 * @package ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\community\user;



if (!defined('CMS')) exit;

require_once(LIBRARY_DIR.'php/form/standard.php');


class Template {


  public static function registrationForm($fields) {
    global $parametersMod;
    global $site;
    $standardForm = new \Library\Php\Form\Standard($fields);
    return $standardForm->generateForm($parametersMod->getValue('community', 'user', 'translations', 'button_register'), $site->generateUrl());
  }

  public static function loginForm($fields) {
    global $parametersMod;
    global $site;
    $standardForm = new \Library\Php\Form\Standard($fields);
    return $standardForm->generateForm($parametersMod->getValue('community', 'user', 'translations', 'button_login'), $site->generateUrl());
  }

  public static function passwordResetForm($fields) {
    global $parametersMod;
    global $site;
    $standardForm = new \Library\Php\Form\Standard($fields);
    return $standardForm->generateForm($parametersMod->getValue('community', 'user', 'translations', 'button_password_reset'), $site->generateUrl());
  }

  public static function profileForm($fields) {
    global $parametersMod;
    global $site;
    $standardForm = new \Library\Php\Form\Standard($fields);
    return $standardForm->generateForm($parametersMod->getValue('community', 'user', 'translations', 'button_update'), $site->generateUrl());
  }

  public static function login($form, $resetLink, $registrationLink) {
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');

    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'title_login'), $level = 1, $layout = null);

    $links = '';
    if($resetLink)
      $links .= '<a href="'.$resetLink.'">'.htmlspecialchars($parametersMod->getValue('community','user','translations','password_reset')).'</a><br />';
    if($registrationLink)
      $links .= '<a href="'.$registrationLink.'">'.htmlspecialchars($parametersMod->getValue('community','user','translations','title_registration')).'</a>';


    $answer .= '
<div class="ipWidget ipWidgetContactForm">
    '.$form.'
    <div class="libPhpFormLinks">
      '.$links.'
    </div>
</div>
    ';

    return $answer;
  }


  public static function passwordReset($form) {
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset'), $level = 1, $layout = null);

    $text = '<p>'.$parametersMod->getValue('community', 'user', 'translations', 'text_password_reset').'</p>';
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);

    $answer .= '
<div class="ipWidget ipWidgetContactForm">
  '.$form.'
</div>
		';

    return $answer;

  }

  public static function passwordResetSentText() {
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset'), $level = 1, $layout = null);

    $text = '<p>'.$parametersMod->getValue('community', 'user', 'translations', 'text_password_reset_sent').'</p>';
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);

    return $answer;
  }

  public static function passwordResetVerified($form) {
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset'), $level = 1, $layout = null);

    $text = '<p>'.$parametersMod->getValue('community', 'user', 'translations', 'text_password_verified').'</p>';
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);

    $answer .= '
<div class="ipWidget ipWidgetContactForm">
  '.$form.'
</div>
  	
  	';

    return $answer;
  }

  public static function passwordResetVerificationError() {
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'title_password_reset'), $level = 1, $layout = null);

    $text = '<p>'.$parametersMod->getValue('community', 'user', 'translations', 'text_password_verification_error').'</p>';
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);

    return $answer;
  }



  public static function registrationVerificationRequired() {
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'title_registration'), $level = 1, $layout = null);

    $text = '<p>'.$parametersMod->getValue('community', 'user', 'translations', 'text_registration_verification_required').'</p>';
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);

    return $answer;
  }

  public static function newEmailVerificationRequired() {
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'title_registration'), $level = 1, $layout = null);

    $text = '<p>'.$parametersMod->getValue('community', 'user', 'translations', 'text_new_email_verification_required').'</p>';
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);

    return $answer;
  }

  public static function registrationVerified($form) {
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'title_registration'), $level = 1, $layout = null);

    $text = $parametersMod->getValue('community', 'user', 'translations', 'text_registration_successful');
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);

    $answer .= '
<div class="ipWidget ipWidgetContactForm">
  '.$form.'
</div>
';
    return $answer;
  }
  public static function registrationVerificationError() {
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'title_registration_verification_error'), $level = 1, $layout = null);

    $text = $parametersMod->getValue('community', 'user', 'translations', 'text_registration_verification_error');
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);

    return $answer;
  }
  public static function verificationErrorUserExist() {
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'title_registration_verification_error'), $level = 1, $layout = null);

    $text = $parametersMod->getValue('community', 'user', 'translations', 'text_user_exist_error');
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);

    return $answer;
  }
  public static function verificationErrorEmailExist() {
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'title_registration_verification_error'), $level = 1, $layout = null);

    $text = $parametersMod->getValue('community', 'user', 'translations', 'text_email_exist_error');
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);

    return $answer;
  }
  public static function newEmailVerificationError() {
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'title_new_email_verification_error'), $level = 1, $layout = null);

    $text = $parametersMod->getValue('community', 'user', 'translations', 'text_new_email_verification_error');
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);

    return $answer;
  }
  public static function profile($form, $updated) {
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'title_profile'), $level = 1, $layout = null);


    if($updated) {
      $text = $parametersMod->getValue('community','user','translations','profile_updated');
      $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);
    }

    $answer .= '
<div class="ipWidget ipWidgetContactForm">
  '.$form.'
</div>
		';
    return $answer;
  }

  public static function registration($form) {
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'title_registration'), $level = 1, $layout = null);

    $answer .= '
<div class="ipWidget ipWidgetContactForm">
  '.$form.'
</div>
 		';

    return $answer;
  }

  public static function registrationdisabledError() {
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'title_registration'), $level = 1, $layout = null);

    $text = $parametersMod->getValue('community', 'user', 'translations', 'text_disabled_registration_error');
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);

    return $answer;
  }

  public static function renewedRegistration() {
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'title_registration'), $level = 1, $layout = null);

    $text = $parametersMod->getValue('community','user','translations','text_account_renewed');
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);

    return $answer;
  }
  public static function renewRegistrationError() {
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'title_registration'), $level = 1, $layout = null);

    $text = $parametersMod->getValue('community','user','translations','text_account_renewal_error');
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);

    return $answer;
  }



}

