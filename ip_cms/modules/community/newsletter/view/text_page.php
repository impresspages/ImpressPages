        global $parametersMod;

        global $site;

        $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
        $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');

        $answer = '';
        $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($parametersMod->getValue('community', 'newsletter', 'subscription_translations', 'newsletter'), 1);
        $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text);

        return $answer;