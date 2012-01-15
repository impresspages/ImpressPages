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