<?php

/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\community\newsletter;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;


class Template{
 


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


