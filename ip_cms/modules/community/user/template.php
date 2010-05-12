<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see licence.html
 */
namespace Modules\community\user;



if (!defined('CMS')) exit;

class Template{

  public static function login($form, $resetLink, $registrationLink){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    
    global $parametersMod;

    $answer = '';
    
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'login_title'), $level = 1, $layout = null);

    $links = '';
    if($resetLink)
      $links .= '<a href="'.$resetLink.'">'.htmlspecialchars($parametersMod->getValue('community','user','translations','password_reset')).'</a><br />';
    if($registrationLink)
      $links .= '<a href="'.$registrationLink.'">'.htmlspecialchars($parametersMod->getValue('community','user','translations','registration_title')).'</a>';

    
    $answer .= '
<div class="ipWidget ipWidgetContactForm">
    '.$form.'
    '.$links.'
</div>
    ';
    
    return $answer;      
  }
  
  
  public static function passwordReset($form){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;
    
    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'password_reset_title'), $level = 1, $layout = null);
    
    $text = '<p>'.$parametersMod->getValue('community', 'user', 'translations', 'password_reset_text').'</p>';
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);
    
    $answer .= '
<div class="ipWidget ipWidgetContactForm">
  '.$form.'
</div>
		';
    
    return $answer;        

  }

  public static function passwordResetSentText(){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'password_reset_title'), $level = 1, $layout = null);

    $text = '<p>'.$parametersMod->getValue('community', 'user', 'translations', 'password_reset_sent_text').'</p>';
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);
    
    return $answer;    
  }
  
  public static function passwordResetVerified($form){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'password_reset_title'), $level = 1, $layout = null);

    $text = '<p>'.$parametersMod->getValue('community', 'user', 'translations', 'password_reset_verified').'</p>';
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);
    
    $answer .= '
<div class="ipWidget ipWidgetContactForm">
  '.$form.'
</div>
  	
  	';
    
    return $answer;        
  }
  
  public static function passwordResetVerificationError(){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';
    
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'password_reset_title'), $level = 1, $layout = null);
    
    $text = '<p>'.$parametersMod->getValue('community', 'user', 'translations', 'password_reset_verification_error').'</p>';
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);
    
    return $answer;        
  }  
  
  
  
  public static function registrationVerificationRequired(){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';
    
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'registration_title'), $level = 1, $layout = null);

    $text = '<p>'.$parametersMod->getValue('community', 'user', 'translations', 'registration_verification_required').'</p>';
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);
    
    return $answer;        
	}
	
  public static function newEmailVerificationRequired(){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';
    
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'registration_title'), $level = 1, $layout = null);

    $text = '<p>'.$parametersMod->getValue('community', 'user', 'translations', 'new_email_verification_required').'</p>';
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);
    
    return $answer;        
	}
  				
  public static function registrationVerified($form){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';
    
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'registration_title'), $level = 1, $layout = null);

    $text = $parametersMod->getValue('community', 'user', 'translations', 'registration_successfull_text');
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);
    
    $answer .= '
<div class="ipWidget ipWidgetContactForm">
  '.$form.'
</div>
';
    return $answer;    	  
	}
  public static function registrationVerificationError(){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';
    
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'registration_verification_error_title'), $level = 1, $layout = null);
    
    $text = $parametersMod->getValue('community', 'user', 'translations', 'registration_verification_error_text');
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);
    
    return $answer;        
	}
  public static function verificationErrorUserExist(){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'registration_verification_error_title'), $level = 1, $layout = null);

    $text = $parametersMod->getValue('community', 'user', 'translations', 'registration_verification_error_user_exist');
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);
    
    return $answer;        
	}		
  public static function verificationErrorEmailExist(){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;

    $answer = '';

    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'registration_verification_error_title'), $level = 1, $layout = null);
    
    $text = $parametersMod->getValue('community', 'user', 'translations', 'registration_verification_error_email_exist');
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);
    
    return $answer;        
	}
  public static function newEmailVerificationError(){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;
    
    $answer = '';
    
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'new_email_verification_error_title'), $level = 1, $layout = null);
    
    $text = $parametersMod->getValue('community', 'user', 'translations', 'new_email_verification_error_text');
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);
    
    return $answer;        
	}
  public static function profile($form, $updated){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;
    
    $answer = '';
    
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'profile_title'), $level = 1, $layout = null);

    
    if($updated){
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
	
  public static function registration($form){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    global $parametersMod;

    $answer = '';
    
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'registration_title'), $level = 1, $layout = null);

    $answer .= '
<div class="ipWidget ipWidgetContactForm">
  '.$form.'
</div>
 		';
    
    return $answer;
	}
	
	public static function registrationdisabledError(){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;
	  
	  $answer = '';
	  
	  $answer .= Modules\standard\content_management\Widgets\text_photos\title::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'registration_title'), $level = 1, $layout = null);

    $text = $parametersMod->getValue('community', 'user', 'translations', 'registration_disabled_error');
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);
	  
		return $answer;
	}
	
  public static function renewedRegistration(){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;
    
    $answer = '';
    
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'registration_title'), $level = 1, $layout = null);
    
    $text = $parametersMod->getValue('community','user','translations','renew_registration_text');
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);
    
    return $answer;        
	}
  public static function renewRegistrationError(){
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
    require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text/template.php');
    global $parametersMod;
    
    $answer = '';
    
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title = $parametersMod->getValue('community', 'user', 'translations', 'registration_title'), $level = 1, $layout = null);
    
    $text = $parametersMod->getValue('community','user','translations','renew_registration_error');
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text, $layout = null);
    
    return $answer;        
	}
  
  
  
}

