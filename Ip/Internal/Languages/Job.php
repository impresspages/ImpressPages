<?php


namespace Ip\Internal\Languages;


class Job
{
    public static function ipRouteLanguage_70($info)
    {
        if (!ipGetOption('Config.multilingual')) {
            return null;
        }

        /** @var \Ip\Request $request */
        $request = $info['request'];

        $result = array(
            'language' => null,
            'relativeUri' => $info['relativeUri']
        );

        $languages = ipContent()->getLanguages();

        if (empty($info['relativeUri'])) {

            if (!empty($_REQUEST['aa']) || !empty($_REQUEST['pa']) || !empty($_REQUEST['sa'])) {
                return null;
            }

            $languages = ipContent()->getLanguages();
            foreach ($languages as $language) {
                if ($language->getUrlPath() == '') {
                    $result['language'] = $language;
                    return $result;
                }
            }
            return null;
        }

        $urlParts = explode('/', rtrim(parse_url($info['relativeUri'], PHP_URL_PATH), '/'), 2);
        if (empty($urlParts[0])) {
            return null;
        }

        $languageUrl = $urlParts[0] . '/';

        $rootLanguage = null;
        foreach ($languages as $language) {
            if ($language->getUrlPath() == $languageUrl) {
                $result['language'] = $language;
                break;
            } elseif ($language->getUrlPath() == '') {
                $rootLanguage = $language;
            }
        }

        if ($result['language']) {
            $result['relativeUri'] = isset($urlParts[1]) ? $urlParts[1] : '';
            return $result;
        }

        if ($rootLanguage) {
            $result['language'] = $rootLanguage;
            return $result;
        }
    }

    public static function ipRequestLanguage_70($info)
    {
        if (!empty($_SESSION['ipLastLanguageId'])) {
            return ipContent()->getLanguage($_SESSION['ipLastLanguageId']);
        }
    }

    public static function ipRequestLanguage_80($info)
    {
        $languages = ipContent()->getLanguages();
        return $languages[0];
    }

}
