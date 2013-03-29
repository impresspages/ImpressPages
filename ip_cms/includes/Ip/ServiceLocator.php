<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Ip;




/**
 *
 * Locate system services
 *
 */
class ServiceLocator
{
    /**
     * @return \Modules\Administrator\Log\Module
     */
    public static function getLog()
    {
        /**
         * @var $log \Modules\Administrator\Log\Module
         */
        global $log;
        return $log;
    }

    /**
     * @return Dispatcher
     */
    public static function getDispatcher()
    {
        /**
         * @var $dispatcher \Ip\Dispatcher
         */
        global $dispatcher;
        return $dispatcher;
    }

    /**
     * @return \Site
     */
    public static function getSite()
    {
        /**
         * @var $site \Site
         */
        global $site;
        return $site;
    }


    /**
     * @return \Frontend\Session
     */
    public static function getSession()
    {
        /**
         * @var $session \Frontend\Session
         */
        global $session;
        return $session;
    }



    /**
     * @return \ParametersMod
     */
    public static function getParametersMod()
    {
        /**
         * @var $session \ParametersMod
         */
        global $parametersMod;
        return $parametersMod;
    }
}