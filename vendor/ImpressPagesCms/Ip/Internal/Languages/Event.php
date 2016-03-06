<?php
/**
 * @package   ImpressPages
 */


/**
 * Created by PhpStorm.
 * User: maskas
 * Date: 16.3.6
 * Time: 23.19
 */

namespace Ip\Internal\Languages;


class Event
{
    /** @var This variable is here just because it is needed for Content/Job.php -> ipRouteAction. Would be nice to remove it */
    public static $multilingualRouteDetected;

    public static function ipInitFinished_10()
    {
        $request = ipRequest();
        $result = ipJob('ipRouteLanguage', array('request' => $request, 'relativeUri' => $request->getRelativePath()));
        if ($result) {
            $requestLanguage = $result['language'];
            $routeLanguage = $requestLanguage->getCode();
            ipRequest()->_setRoutePath($result['relativeUri']);
        } else {
            $routeLanguage = null;
            $requestLanguage = ipJob('ipRequestLanguage', array('request' => $request));
            ipRequest()->_setRoutePath($request->getRelativePath());
        }
        //find out and set locale
        $locale = $requestLanguage->getCode();
        if (strlen($locale) == '2') {
            $locale = strtolower($locale) . '_' . strtoupper($locale);
        } else {
            $locale = str_replace('-', '_', $locale);
        }
        $locale .= '.utf8';
        if($locale ==  "tr_TR.utf8" && (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 5)) { //Overcoming this bug https://bugs.php.net/bug.php?id=18556
            setlocale(LC_COLLATE, $locale);
            setlocale(LC_MONETARY, $locale);
            setlocale(LC_NUMERIC, $locale);
            setlocale(LC_TIME, $locale);
            setlocale(LC_MESSAGES, $locale);
            setlocale(LC_CTYPE, "en_US.utf8");
        } else {
            setLocale(LC_ALL, $locale);
        }
        setlocale(LC_NUMERIC, "C"); //user standard C syntax for numbers. Otherwise you will get funny things with when autogenerating CSS, etc.

        ipContent()->_setCurrentLanguage($requestLanguage);

        $_SESSION['ipLastLanguageId'] = $requestLanguage->getId();

        $translator = \Ip\ServiceLocator::translator();
        $translator->setLocale($locale);

        self::$multilingualRouteDetected = $routeLanguage != null;
    }

}
