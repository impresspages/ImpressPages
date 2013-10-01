<?php
/**
 * @package   ImpressPages
 */

namespace Ip;


/**
 * Class to detect browser capabilities
 * @package Ip
 */
class Browser
{
    public static function isMobile()
    {
        if (isset($_SESSION['lib']['browser']['isMobile'])) {
            return (bool) $_SESSION['lib']['browser']['isMobile'];
        }
        $detect = new \Ip\Lib\MobileDetect();
        $isMobile = $detect->isMobile();
        $_SESSION['lib']['browser']['isMobile'] = $isMobile;
        return $isMobile;
    }
}