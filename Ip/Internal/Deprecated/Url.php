<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Deprecated;

class Url {
    static $otherZones = null;



    /**
     * Generate link to website. Use it with no arguments to get link to main page of current language.
     *
     * Don't use it to generate link to existing page. To get link to existing page, use method getLink() on Element object.
     *
     * @param $languageId
     *   Id of language
     * @param $zoneName
     *   Zone name
     * @param $urlVars
     *   Array of additional url variables. Eg. array('var1', 'var2')
     * @param $getVars
     *   Array of additional get variables. Eg. array('var1'='val1', 'val2'='val2')
     * @param $escape
     *   Escape & with &amp;
     * @return string - requested link or link to first page of current language if all parameters are not specified or null
     */
    public static function generate($languageId=null, $zoneName = null, $urlVars = null, $getVars = null, $escape = true){
        if($languageId == null){
            $languageId = ipContent()->getCurrentLanguage()->getId();
        }


        if (ipGetOption('Config.multilingual')) {
            $answer = ipConfig()->baseUrl() . urlencode(\Ip\ServiceLocator::content()->getLanguageById($languageId)->getUrl()).'/';
        } else {
            $answer = ipConfig()->baseUrl();
        }

        if ($zoneName != null){
            if ($languageId == ipContent()->getCurrentLanguage()->getId()){ //current language
                $zone = ipContent()->getZone($zoneName);
                if ($zone) {
                    if ($zone->getUrl()) {
                        $answer .= urlencode($zone->getUrl()).'/';
                    }
                } else {
                    $backtrace = debug_backtrace();
                    if (isset($backtrace[0]['file']) && $backtrace[0]['line']) {
                        trigger_error('Undefined zone '.$zoneName.' (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
                    } else {
                        trigger_error('Undefined zone '.$zoneName);
                    }
                    return '';
                }
            } else {
                if (!isset(self::$otherZones[$languageId])) {
                    self::$otherZones[$languageId] = \Ip\Internal\ContentDb::getZones($languageId);
                }

                if (isset(self::$otherZones[$languageId])) {
                    $answer .= urlencode(self::$otherZones[$languageId][$zoneName]['url']) . '/';
                } else {
                    $backtrace = debug_backtrace();
                    if (isset($backtrace[0]['file']) && $backtrace[0]['line']) {
                        trigger_error('Undefined zone '.$zoneName.' (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
                    } else {
                        trigger_error('Undefined zone '.$zoneName);
                    }
                    return '';
                }
            }


        }

        if ($urlVars) {
            foreach ($urlVars as $value) {
                $answer .= urlencode($value).'/';
            }
        }


        if ($escape) {
            $amp = '&amp;';
        } else {
            $amp = '&';
        }

        if ($getVars && sizeof($getVars) > 0) {
            $answer .= '?'.http_build_query($getVars, '', $amp);
        }

        return $answer;
    }
}