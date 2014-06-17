<?php
/**
 * @package   ImpressPages
 */


namespace Ip\Internal\Config;


class Event
{
    public static function ipLanguageAdded($data)
    {
        $language = ipContent()->getLanguage($data['id']);
        $languages = ipContent()->getLanguages();
        $firstLanguage = $languages[0];
        $title = ipGetOptionLang('Config.websiteTitle', $firstLanguage->getCode());
        $email = ipGetOptionLang('Config.websiteEmail', $firstLanguage->getCode());
        ipSetOptionLang('Config.websiteTitle', $title, $language->getCode());
        ipSetOptionLang('Config.websiteEmail', $email, $language->getCode());
    }

    public static function ipLanguageUpdated($data)
    {
        $oldCode = $data['old']['code'];
        $newCode = $data['new']['code'];
        if ($oldCode != $newCode) {
            $title = ipGetOptionLang('Config.websiteTitle', $oldCode);
            $email = ipGetOptionLang('Config.websiteEmail', $oldCode);
            ipRemoveOptionLang('Config.websiteTitle', $oldCode);
            ipRemoveOptionLang('Config.websiteEmail', $oldCode);
            ipSetOptionLang('Config.websiteTitle', $title, $newCode);
            ipSetOptionLang('Config.websiteEmail', $email, $newCode);
        }
    }
}
