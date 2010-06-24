<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see licence.html
 */
namespace Modules\community\user;

if (!defined('CMS')) exit;

require_once(LIBRARY_DIR.'php/form/standard.php');
require_once(BASE_DIR.MODULE_DIR."community/user/db.php");

/**
 * User area (registration) configuration.
 * @package ImpressPages 
 */  

class Config{
	/** @var password hash salt*/
  public static $hashSalt = '';
	

  public static $urlPasswordReset = 'password-reset';
  public static $urlPasswordResetVerified = 'password-reset-verified';
  public static $urlPasswordResetVerificationError = 'password-reset-verification-error';
  public static $urlPasswordResetSentText = 'password-reset-sent-text';
  public static $urlRegistrationVerificationRequired = 'registration-verification-required';
  public static $urlEmailVerificationRequired = 'new-email-verification-required';
  public static $urlRegistrationVerified = 'registration-verified';
  public static $urlNewEmailVerified = 'new-email-verified';
  public static $urlRegistrationVerificationError = 'registration-verification-error';
  public static $urlNewEmailVerificationError = 'new-email-verification-error';
  public static $urlLogin = 'login';
  public static $urlProfile = 'profile';
  public static $urlRegistration = 'registration';
  public static $urlRenewedRegistration = 'renewed-registration';
  public static $urlRenewRegistrationError = 'renew-registration-error';
  public static $urlVerificationErrorUserExist = 'verification-error-user-exist';
  public static $urlVerificationErrorEmailExist = 'verification-error-email-exist';
	
	
	
  /** @var array fields, that are used for registration */
  public static function getRegistrationFields(){
    /** private*/
    global $parametersMod;
    /** private*/
    global $session;    
    
    $registrationFields = array();
    
    /*hidden fields (required)*/
    $field = new \Library\Php\Form\FieldHidden();
    $field->name = 'action';
    $field->value = 'register';
    $registrationFields[] = $field;

    $field = new \Library\Php\Form\FieldHidden();
    $field->name = 'module_group';
    $field->value = 'community';
    $registrationFields[] = $field;

    $field = new \Library\Php\Form\FieldHidden();
    $field->name = 'module_name';
    $field->value = 'user';
    $registrationFields[] = $field;       
    
    /*predefined fields (required)*/    
    if($parametersMod->getValue('community','user','options','login_type') == 'login'){
      $field = new \Library\Php\Form\FieldText();
      $field->name = 'login';
      $field->dbField = 'login';
      $field->caption = $parametersMod->getValue('community','user','translations','field_login');
      $field->required = true;
      $registrationFields[] = $field;
    }
    
    

    $field = new \Library\Php\Form\FieldEmail();
    $field->dbField = 'email';
    $field->name = 'email';
    $field->caption = $parametersMod->getValue('community','user','translations','field_email');
    $field->required = true;
    $registrationFields[] = $field;

    $field = new \Library\Php\Form\FieldPassword();
    $field->name = 'password';
    $field->caption = $parametersMod->getValue('community','user','translations','field_password');
    $field->required = true;
    $registrationFields[] = $field;

    if($parametersMod->getValue('community','user','options','type_password_twice')){
      $field = new \Library\Php\Form\FieldPassword();
      $field->name = 'confirm_password';
      $field->required = true;
      $field->caption = $parametersMod->getValue('community','user','translations','field_confirm_password');
      $registrationFields[] = $field;
    }
    
    /*add your additional fields*/
    
    return $registrationFields;
  }
    
  /** @var array fields, that are used for profile page */
  public static function getProfileFields(){
    /** private*/
    global $parametersMod;
    /** private*/
    global $session;    
  
    $dbMod = new Db(); 

    $profileFields = array();
    
    /*hidden fields (required)*/
    $field = new \Library\Php\Form\FieldHidden();
    $field->name = 'action';
    $field->value = 'update_profile';
    $profileFields[] = $field;

    $field = new \Library\Php\Form\FieldHidden();
    $field->name = 'module_group';
    $field->value = 'community';
    $profileFields[] = $field;

    $field = new \Library\Php\Form\FieldHidden();
    $field->name = 'module_name';
    $field->value = 'user';
    $profileFields[] = $field;
    
    
    /*predefined fields (required)*/    
    if($session->loggedIn()){ //security check
      $user = $dbMod->userById($session->userId());

      
      $field = new \Library\Php\Form\FieldEmail();
      $field->name = 'email';
      $field->caption = $parametersMod->getValue('community','user','translations','field_email');
      $field->required = true;
      $field->value = $user['email'];
      $profileFields[] = $field;        
      

  
      $field = new \Library\Php\Form\FieldPassword();
      $field->name = 'password';
      $field->caption = $parametersMod->getValue('community','user','translations','field_password');
      $profileFields[] = $field;

      if($parametersMod->getValue('community','user','options','type_password_twice')){
        $field = new \Library\Php\Form\FieldPassword();
        $field->name = 'confirm_password';
        $field->caption = $parametersMod->getValue('community','user','translations','field_confirm_password');
        $profileFields[] = $field;
      }

      
  
    }
  
    /*add your additional fields*/
  
  
    return $profileFields;
  }
    
    
  /** @var array fields, that are used for user login */
  public static function getLoginFields(){
    /** private*/
    global $parametersMod;
    /** private*/
    global $session;    
  
    $loginFields = array();
  
    /*hidden fields (required)*/
    $field = new \Library\Php\Form\FieldHidden();
    $field->name = 'action';
    $field->value = 'login';
    $loginFields[] = $field;

    $field = new \Library\Php\Form\FieldHidden();
    $field->name = 'module_group';
    $field->value = 'community';
    $loginFields[] = $field;

    $field = new \Library\Php\Form\FieldHidden();
    $field->name = 'module_name';
    $field->value = 'user';
    $loginFields[] = $field;
  
  
    /*predefined fields (required)*/    
    if($parametersMod->getValue('community','user','options','login_type') == 'login'){
      $field = new \Library\Php\Form\FieldText();
      $field->name = 'login';
      $field->caption = $parametersMod->getValue('community','user','translations','field_login');
      $field->required = true;
      $loginFields[] = $field;
    }else{
      $field = new \Library\Php\Form\FieldEmail();
      $field->name = 'email';
      $field->caption = $parametersMod->getValue('community','user','translations','field_email');
      $field->required = true;
      $loginFields[] = $field;
    }
  


    $field = new \Library\Php\Form\FieldPassword();
    $field->name = 'password';
    $field->caption = $parametersMod->getValue('community','user','translations','field_password');
    $field->required = true;
    $loginFields[] = $field;
    
    /*add your additional fields*/
    
    
    return $loginFields;
  }
  
  /** @var array fields, that are used for user password reset */
  public static function getPasswordResetFields(){
    /** private*/
    global $parametersMod;
    /** private*/
    global $session;    

    $passwordResetFields = array();
    
    /*hidden fields (required)*/    
    $field = new \Library\Php\Form\FieldHidden();
    $field->name = 'action';
    $field->value = 'password_reset';
    $passwordResetFields[] = $field;

    $field = new \Library\Php\Form\FieldHidden();
    $field->name = 'module_group';
    $field->value = 'community';
    $passwordResetFields[] = $field;

    $field = new \Library\Php\Form\FieldHidden();
    $field->name = 'module_name';
    $field->value = 'user';
    $passwordResetFields[] = $field;  

    /*predefined fields (required)*/  

    $field = new \Library\Php\Form\FieldEmail();
    $field->db_field = 'email';
    $field->name = 'email';
    $field->caption = $parametersMod->getValue('community','user','translations','field_email');
    $field->required = true;
    $passwordResetFields[] = $field;


    $field = new \Library\Php\Form\FieldPassword();
    $field->name = 'password';
    $field->caption = $parametersMod->getValue('community','user','translations','field_password');
    $field->required = true;
    $passwordResetFields[]  = $field;

    if($parametersMod->getValue('community','user','options','type_password_twice')){
      $field = new \Library\Php\Form\FieldPassword();
      $field->name = 'confirm_password';
      $field->required = true;
      $field->caption = $parametersMod->getValue('community','user','translations','field_confirm_password');
      $passwordResetFields[]  = $field;
    }

    /*you are able to add additional fields, but i think you don't need to*/
    
    return $passwordResetFields;  
  }

}
