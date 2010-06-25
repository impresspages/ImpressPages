<?php

/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\community\newsletter; 

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;


class Template{
	static function registration($newsletterUrl, $unsubscriptionButton){
		global $site;
		global $parametersMod;
		
		if ($unsubscriptionButton) {
  		$unsubscriptionButtonHtml = '<a href="#" class="unsubscribe" onclick="ModCommunityNewsletter.unsubscribe(\''.$newsletterUrl.'\', document.getElementById(\'modCommunityNewsletterRegister\').email.value); return false;">'.htmlspecialchars($parametersMod->getValue('community', 'newsletter', 'subscription_translations', 'unsubscribe')).'</a>';
		} else {
		  $unsubscriptionButtonHtml = '';
		}
		
		return '
<script src="'.BASE_URL.MODULE_DIR.'community/newsletter/newsletter.js" type="text/javascript"></script>
<span class="label">'.$parametersMod->generateManagement('community', 'newsletter', 'subscription_translations', 'newsletter').'</span>
<span id="modCommunityNewsletterError" class="error">'.strip_tags($parametersMod->getValue('community', 'newsletter', 'subscription_translations', 'text_incorrect_email')).'</span>
<form id="modCommunityNewsletterRegister" onsubmit="ModCommunityNewsletter.subscribe(\''.$newsletterUrl.'\', document.getElementById(\'modCommunityNewsletterRegister\').email.value); return false;" method="post" action=""> 
  <div>
    <input type="text" name="email" class="input" /> 
    <input type="hidden" name="action" value="subscribe" />
  </div>
  <div>
    <a href="#" class="subscribe" onclick="ModCommunityNewsletter.subscribe(\''.$newsletterUrl.'\', document.getElementById(\'modCommunityNewsletterRegister\').email.value); return false;">'.htmlspecialchars($parametersMod->getValue('community', 'newsletter', 'subscription_translations', 'subscribe')).'</a>
    '.$unsubscriptionButtonHtml.'
    <div class="clear"><!-- --></div>
  </div> 
</form>
		';
	}	

  static function textPage($text){
    global $parametersMod;
    
    global $site;

    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    
    $answer = '';
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($parametersMod->getValue('community', 'newsletter', 'subscription_translations', 'newsletter'), 1);
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text);

    return $answer;
	}
	
	static function newsletterTemplate($languageId, $text, $unsubscribeLink){
    require_once(BASE_DIR.LIBRARY_DIR.'php/text/system_variables.php');

	  global $parametersMod;

    $unsubscribeHtml = "\n".'<a href="'.$unsubscribeLink.'">'.htmlspecialchars($parametersMod->getValue('community', 'newsletter', 'subscription_translations', 'unsubscribe', $languageId)).'</a>'."\n";

		$email = $parametersMod->getValue('standard','configuration','main_parameters','email_template', $languageId);
		$email = str_replace('[[content]]', $text, $email);
		$email = str_replace('[[unsubscribe]]', $unsubscribeHtml, $email);
    $email = \Library\Php\Text\SystemVariables::insert($email, $languageId);
    $email = \Library\Php\Text\SystemVariables::clear($email, $languageId);

		$email = '
<html>
  <head></head>
	<body>
    '.$email.'
  </body>
</html>
';
	  return $email;
	}
	
	static function subscribeConfirmation($link){
    require_once(BASE_DIR.LIBRARY_DIR.'php/text/system_variables.php');
	  require_once(BASE_DIR.LIBRARY_DIR.'php/text/html_transform.php');

	  global $parametersMod;

    $emailHtml = str_replace('[[content]]', $parametersMod->getValue('community', 'newsletter', 'subscription_translations', 'text_email_confirmation'), $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email_template')); 
		$emailHtml = str_replace('[[link]]', '<a href="'.$link.'">'.\Library\Php\Text\HtmlTransform::prepareLink($link).'</a>', $emailHtml);
    $emailHtml = \Library\Php\Text\SystemVariables::insert($emailHtml);
    $emailHtml = \Library\Php\Text\SystemVariables::clear($emailHtml);

	  return $emailHtml;	  
	}

}


