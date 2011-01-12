<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license		GNU/GPL, see licence.html
 */

namespace Modules\community\user;



if (!defined('CMS')) exit;

global $site;
$site->requireTemplate("community/user/template.php");

require_once('element.php');

global $site;
$site->requireConfig('community/user/config.php');


class Zone extends \Frontend\Zone {
  var $zoneName;
  function __construct($key) {
    $this->zoneName = $key;
  }



  /**
   * Finds all pages of current zone
   * @return array elements
   */
  public function getElements($language = null, $parentElementId = null, $startFrom = 0, $limit = null, $includeHidden = false, $reverseOrder = false) {
    if($parentElementId == null) {
      $elements = array();

      $elements[] = new Element('password_reset', $this->name);
      //$elements[] = new Element('password_reset_verified', $this->name);
      //$elements[] = new Element('password_reset_verification_error', $this->name);
      //$elements[] = new Element('password_reset_sent_text', $this->name);
      //$elements[] = new Element('registration_verification_required', $this->name);
      //$elements[] = new Element('new_email_verification_required', $this->name);
      //$elements[] = new Element('registration_verified', $this->name);
      //$elements[] = new Element('new_email_verified', $this->name);
      //$elements[] = new Element('registration_verification_error', $this->name);
      //$elements[] = new Element('new_email_verification_error', $this->name);
      $elements[] = new Element('login', $this->name);
      $elements[] = new Element('profile', $this->name);
      $elements[] = new Element('registration', $this->name);
      //$elements[] = new Element('renewed_registration', $this->name);
      //$elements[] = new Element('renew_registration_error', $this->name);
      //$elements[] = new Element('verification_error_user_exist', $this->name);
      //$elements[] = new Element('verification_error_email_exist', $this->name);

 
      return $elements;
    } else {
      return false;
    }

  }


  /**
   * @param int $elementId
   * @return array element
   */
  public  function getElement($elementId) {
    return new Element(null, $this->name); //default zone return element with all url and get variable combinations
  }

  /**
   * @param int $elementId
   * @return string link to specified element
   */
  public function generateUrl($elementId) {
  }


  /**
   * @param array $urlVars
   * @return array element
   */
  public function findElement($urlVars, $getVars) {


    global $site;
    global $parametersMod;
    if(sizeof($urlVars)> 0) {
      switch($urlVars[0]) {
        case Config::$urlPasswordReset:
          if($parametersMod->getValue('community', 'user', 'options', 'allow_password_reset'))
            return new Element('password_reset', $this->name);
          break;
        case Config::$urlPasswordResetVerified:
          if($parametersMod->getValue('community', 'user', 'options', 'allow_password_reset'))
            return new Element('password_reset_verified', $this->name);
          break;
        case Config::$urlPasswordResetVerificationError:
          if($parametersMod->getValue('community', 'user', 'options', 'allow_password_reset'))
            return new Element('password_reset_verification_error', $this->name);
          break;
        case Config::$urlPasswordResetSentText:
          if($parametersMod->getValue('community', 'user', 'options', 'allow_password_reset')) {
            return new Element('password_reset_sent_text', $this->name);
          }
          break;
        case Config::$urlRegistrationVerificationRequired:
          return new Element('registration_verification_required', $this->name);
          break;
        case Config::$urlEmailVerificationRequired:
          return new Element('new_email_verification_required', $this->name);
          break;
        case Config::$urlRegistrationVerified:
          return new Element('registration_verified', $this->name);
          break;
        case Config::$urlNewEmailVerified:
          return new Element('new_email_verified', $this->name);
          break;
        case Config::$urlRegistrationVerificationError:
          return new Element('registration_verification_error', $this->name);
          break;
        case Config::$urlNewEmailVerificationError:
          return new Element('new_email_verification_error', $this->name);
          break;
        case Config::$urlLogin:
          return new Element('login', $this->name);
          break;
        case Config::$urlProfile:
          return new Element('profile', $this->name);
          break;
        case Config::$urlRegistration:
          return new Element('registration', $this->name);
          break;
        case Config::$urlRenewedRegistration:
          return new Element('renewed_registration', $this->name);
          break;
        case Config::$urlRenewRegistrationError:
          return new Element('renew_registration_error', $this->name);
          break;
        case Config::$urlVerificationErrorUserExist:
          return new Element('verification_error_user_exist', $this->name);
          break;
        case Config::$urlVerificationErrorEmailExist:
          return new Element('verification_error_email_exist', $this->name);
          break;
        default:
          return false;
          break;
      }
    } else {
      return new Element('login', $this->name);
    }


  }

  function generateContent($element) {

  }


  function generateManagement($element) {
    return $this->generateContent($element);
  }




  /**
   * @return string html form
   */
  public static function generateRegistration() {
    global $parametersMod;
    global $site;
    $standardForm = new \Library\Php\Form\Standard(Config::getRegistrationFields());
    return $standardForm->generateForm($parametersMod->getValue('community', 'user', 'translations', 'button_register'), $site->generateUrl());

  }

  /**
   * @return string html form
   */
  public static function generateLogin() {
    global $parametersMod;
    global $site;

    $standardForm = new \Library\Php\Form\Standard(Config::getLoginFields());
    return $standardForm->generateForm($parametersMod->getValue('community', 'user', 'translations', 'button_login'), $site->generateUrl());
  }

  /**
   * @return string html form
   */
  public static function generatePasswordReset() {
    global $parametersMod;
    global $site;
    $standardForm = new \Library\Php\Form\Standard(Config::getPasswordResetFields());
    return $standardForm->generateForm($parametersMod->getValue('community', 'user', 'translations', 'button_password_reset'), $site->generateUrl());

  }


  /**
   * @return string html form
   */
  public static function generateProfile() {
    global $parametersMod;
    global $site;
    global $session;

    if($session->loggedIn()) {
      $standardForm = new \Library\Php\Form\Standard(Config::getProfileFields());
      return $standardForm->generateForm($parametersMod->getValue('community', 'user', 'translations', 'button_update'), $site->generateUrl());
    }else {
      $userZone = $site->getZoneByModule('community', 'user');
      return '<script type="text/javascript">document.location=\''.$site->generateUrl(null, $userZone->getName(), array(Config::$urlLogin)).'\'</script>';
    }

  }

  /**
   * @return string link to registration page
   */
  public static function getLinkRegistration() {
    global $site;
    $zone = $site->getZoneByModule('community', 'user');
    if($zone)
      return $site->generateUrl(null, $zone->getName(), array(Config::$urlRegistration));

  }

  /**
   * @return string link to profile page
   */
  public static function getLinkProfile() {
    global $site;
    $zone = $site->getZoneByModule('community', 'user');
    if($zone)
      return $site->generateUrl(null, $zone->getName(), array(Config::$urlProfile));

  }

  /**
   * @return string link to login page
   */
  public static  function getLinkLogin() {
    global $site;
    $zone = $site->getZoneByModule('community', 'user');
    if($zone)
      return $site->generateUrl(null, $zone->getName(), array(Config::$urlLogin));

  }

  /**
   * @return string link to logout
   */
  public static function getLinkLogout() {
    global $site;
    return $site->generateUrl(null, null, null, array('module_group' => 'community', 'module_name' => 'user', 'action'=>'logout'));
  }

  /**
   * @return string link to password reset service
   */
  public static  function getLinkPasswordReset() {
    global $site;
    $zone = $site->getZoneByModule('community', 'user');
    if($zone)
      return $site->generateUrl(null, $zone->getName(), array(Config::$urlPasswordReset));

  }



}
