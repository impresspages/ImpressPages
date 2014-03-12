<?php


namespace Ip\Internal\Languages;


class Job
{
    public static function ipRouteLanguage_70($info)
    {
        if (!ipGetOption('Config.multilingual') || empty($info['relativeUri'])) {
            return null;
        }

        /** @var \Ip\Request $request */
        $request = $info['request'];

        // admin pages don't have zones
//        if (!empty($_SESSION['ipLastLanguageId'])) {
//            $language = ipContent()->getLanguage($_SESSION['ipLastLanguageId']);
//        }
//        if (!$language) {
//            $language = $languages[0];
//        }

        $result = array(
            'language' => null,
            'relativeUri' => $info['relativeUri']
        );

        $urlParts = explode('/', rtrim(parse_url($info['relativeUri'], PHP_URL_PATH), '/'), 2);
        if (empty($urlParts[0])) {
            return null;
        }

        $languageUrl = urldecode($urlParts[0]);

        $languages = ipContent()->getLanguages();
        foreach ($languages as $language) {
            if ($language->getUrl() == $languageUrl) {
                $result['language'] = $language;
                break;
            }
        }

        if ($result['language']) {
            $result['relativeUri'] = isset($urlParts[1]) ? $urlParts[1] : '';
            return $result;
        }
    }

    public static function ipRouteLanguage_75($info)
    {
        if (!empty($_SESSION['ipLastLanguageId'])) {
            return array(
                'language' => ipContent()->getLanguage($_SESSION['ipLastLanguageId']),
                'relativeUri' => $info['relativeUri']
            );
        }
    }

    public static function ipRouteLanguage_80($info)
    {
        $languages = ipContent()->getLanguages();
        return array(
            'language' => $languages[0],
            'relativeUri' => $info['relativeUri']
        );
    }
}
