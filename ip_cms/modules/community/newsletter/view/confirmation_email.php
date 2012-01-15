        require_once(BASE_DIR.LIBRARY_DIR.'php/text/system_variables.php');
        require_once(BASE_DIR.LIBRARY_DIR.'php/text/html_transform.php');

        global $parametersMod;

        $emailHtml = str_replace('[[content]]', $parametersMod->getValue('community', 'newsletter', 'subscription_translations', 'text_email_confirmation'), $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email_template'));
        $emailHtml = str_replace('[[link]]', '<a href="'.$link.'">'.\Library\Php\Text\HtmlTransform::prepareLink($link).'</a>', $emailHtml);
        $emailHtml = \Library\Php\Text\SystemVariables::insert($emailHtml);
        $emailHtml = \Library\Php\Text\SystemVariables::clear($emailHtml);

        return $emailHtml;