<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal;


/**
 * Class to detect browser capabilities
 * @package Ip
 */
class Browser
{
    public static function isMobile()
    {
        if (defined('IS_MOBILE')) {
            return (bool)IS_MOBILE;
        }

        if (isset($_SESSION['lib']['browser']['isMobile'])) {
            return (bool)$_SESSION['lib']['browser']['isMobile'];
        }
        $detect = new \Ip\Lib\MobileDetect();
        $isMobile = $detect->isMobile();
        $_SESSION['lib']['browser']['isMobile'] = $isMobile;
        return $isMobile;
    }


    public static function getLanguages()
    {
        $answer = Array();
        if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
            $lang_list = explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
            for ($i = 0; $i < count($lang_list); $i++) {
                if (strpos($lang_list[$i], ";") === false) {
                    $answer[] = $lang_list[$i];
                } else {
                    $tmp_array = explode(";", $lang_list[$i]);
                    $answer[] = $tmp_array[0];
                }
            }
        }
        return $answer;
    }
}