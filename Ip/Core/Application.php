<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Core;


class Application {

    protected static $isInitFinished = false;

    public static function init()
    {
        if (static::$isInitFinished) {
            return;
        }

        define('IP_VERSION', '3.6');

        require (BASE_DIR.INCLUDE_DIR.'parameters.php');
        require (BASE_DIR.INCLUDE_DIR.'db.php');

        require (CORE_DIR.'Ip/Site.php');
        require (BASE_DIR.MODULE_DIR.'administrator/log/module.php');
        require (BASE_DIR.INCLUDE_DIR.'error_handler.php');

        if(!\Db::connect()){
            trigger_error("Database access");
        }

        global $log;
        $log = new \Modules\Administrator\Log\Module();
        global $dispatcher;
        $dispatcher = new \Ip\Dispatcher();
        global $parametersMod;
        $parametersMod = new \parametersMod();
        global $session;
        $session = new \Ip\Frontend\Session();
        global $site;
        $site = new \Site();

        mb_internal_encoding(CHARSET);
        date_default_timezone_set(\Ip\Config::getRaw('timezone')); //PHP 5 requires timezone to be set.

        if (DEVELOPMENT_ENVIRONMENT){
            error_reporting(E_ALL|E_STRICT);
            ini_set('display_errors', '1');
        } else {
            ini_set('display_errors', '0');
        }

        static::$isInitFinished = true;
    }
}