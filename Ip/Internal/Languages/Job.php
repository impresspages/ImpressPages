<?php


namespace Ip\Internal\Languages;


use Ip\Internal\Install\PageAssets;
use Ip\Language;

class Job
{
    public static function ipRouteLanguage_70($info)
    {
        /** @var \Ip\Request $request */
        $request = $info['request'];

        $result = array(
            'language' => null,
            'relativeUri' => $info['relativeUri']
        );

        $languages = ipContent()->getLanguages();

        if (empty($info['relativeUri'])) {

            if ($request->getRequest('aa') || $request->getRequest('pa') || $request->getRequest('sa')) {
                return null;
            }

            $languages = ipContent()->getLanguages();
            if (!empty($languages)) {
                $result['language'] = $languages[0];
            } else {
                $result['language'] = self::fakeEnglish();
            }

            return $result;
        }

        $urlParts = explode('/', rtrim(parse_url($info['relativeUri'], PHP_URL_PATH), '/'), 2);
        if (empty($urlParts[0])) {
            return null;
        }

        $languageUrl = $urlParts[0] . '/';

        foreach ($languages as $language) {
            if ($language->getUrlPath() == $languageUrl) {
                $result['language'] = $language;
                break;
            }
        }

        if ($result['language']) {
            $result['relativeUri'] = isset($urlParts[1]) ? $urlParts[1] : '';
            return $result;
        }

        if ($languages[0]->getUrlPath() === '') {
            $result['language'] = $languages[0];
            return $result;
        }
        return null;
    }

    public static function ipRequestLanguage_70($info)
    {
        if (!empty($_SESSION['ipLastLanguageId'])) {
            $language = ipContent()->getLanguage($_SESSION['ipLastLanguageId']);
            if ($language) { //language may be missing if user has just deleted the language stored in the session
                return $language;
            }
        }
        return null;
    }

    public static function ipRequestLanguage_80($info)
    {
        $languages = ipContent()->getLanguages();
        if (!empty($languages)) {
            return $languages[0];
        }
        return self::fakeEnglish();
    }

    protected static function fakeEnglish()
    {
        return new Language(null, 'en', 'en', 'English', 'EN', '1', 'ltr');
    }
}
