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

        require (BASE_DIR . CORE_DIR.'Ip/Site.php');
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

    public static function run()
    {
        global $site, $log, $parametersMod, $dispatcher;

        $site->init();
        $site->dispatchEvent('administrator', 'system', 'init', array());
        $dispatcher->notify(new \Ip\Event($site, 'site.afterInit', null));

        /*detect browser language*/
        if((!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == '') && $parametersMod->getValue('standard', 'languages', 'options', 'detect_browser_language') && $site->getCurrentUrl() == BASE_URL && !isset($_SESSION['modules']['standard']['languages']['language_selected_by_browser']) && $parametersMod->getValue('standard', 'languages', 'options', 'multilingual')){
            require_once(BASE_DIR.LIBRARY_DIR.'php/browser_detection/language.php');

            $browserLanguages = \Library\Php\BrowserDetection\Language::getLanguages();
            $selectedLanguageId = null;
            foreach($browserLanguages as $browserLanguageKey => $browserLanguage){
                foreach($site->languages as $siteLanguageKey => $siteLanguage){
                    if(strpos($browserLanguage, '-') !== false) {
                        $browserLanguage = substr($browserLanguage, 0, strpos($browserLanguage, '-'));
                    }
                    if(strpos($siteLanguage['code'], '-') !== false) {
                        $siteLanguage['code'] = substr($siteLanguage['code'], 0, strpos($siteLanguage['code'], '-'));
                    }

                    if($siteLanguage['code'] == $browserLanguage){
                        $selectedLanguageId = $siteLanguage['id'];
                        break;
                    }
                }
                if ($selectedLanguageId != null) {
                    break;
                }
            }

            if($selectedLanguageId != $site->currentLanguage['id'] && $selectedLanguageId !== null)
                header("location:".$site->generateUrl($selectedLanguageId));
        }
        $_SESSION['modules']['standard']['languages']['language_selected_by_browser'] = true;
        /*eof detect browser language*/

        /*check if the website is closed*/
        if($parametersMod->getValue('standard', 'configuration', 'main_parameters', 'closed_site') && !$site->managementState()
            && (!\Ip\Backend::loggedIn() || !isset($_REQUEST['g']) || !isset($_REQUEST['m']) || !isset($_REQUEST['a']))){
            echo $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'closed_site_message');
            \Db::disconnect();
            exit;
        }
        /*eof check if the website is closed*/

        if(!defined('BACKEND')){
            $session = \Ip\ServiceLocator::getSession();
            if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
                $parametersMod->getValue('standard', 'configuration', 'advanced_options', 'xss_autocheck') &&
                (empty($_POST['securityToken']) || $_POST['securityToken'] !=  $session->getSecurityToken()) &&
                (empty($_POST['pa']) || empty($_POST['m']) || empty($_POST['g']))
            ) {
                $data = array(
                    'status' => 'error',
                    'errors' => array(
                        'securityToken' => $parametersMod->getValue('developer', 'form', 'error_messages', 'xss')
                    )
                );
                header('Content-type: text/json; charset=utf-8'); //throws save file dialog on firefox if iframe is used
                echo json_encode($data);
                \Db::disconnect();
                $dispatcher->notify(new \Ip\Event($site, 'site.databaseDisconnect', null));
                exit;
            }


            $site->makeActions(); //all posts are handled by "site" and redirected to current module actions.php before any output.


            if (!$site->managementState() && !\Ip\Module\Design\ConfigModel::instance()->isInPreviewState()) {
                $site->makeRedirect(); //if required;
            }
        }

        if (defined('WORKER')) {

            global $site;
            global $log;

            $site = \Ip\ServiceLocator::getSite();

            $log = new \Modules\administrator\log\Module();

            global $cms;
            $cms = new \Ip\Backend\Cms();

            $cms->worker();

            \Db::disconnect();
            exit();
        }

        $output = $site->generateOutput();

        $dispatcher->notify(new \Ip\Event($site, 'site.outputGenerated', array('output' => &$output)));
        echo $output;
        $dispatcher->notify(new \Ip\Event($site, 'site.outputPrinted', array('output' => &$output)));


        /*
         Automatic execution of cron.
        The best solution is to setup cron service to launch file www.yoursite.com/ip_cron.php few times a day.
        By default fake cron is enabled
        */
        if(!\Ip\Module\Admin\Model::isSafeMode() && $parametersMod->getValue('standard', 'configuration', 'advanced_options', 'use_fake_cron') && function_exists('curl_init') && $log->lastLogsCount(60, 'system/cron') == 0){
            // create a new curl resource

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, BASE_URL.'ip_cron.php');
            curl_setopt($ch, CURLOPT_REFERER, BASE_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            $fakeCronAnswer = curl_exec($ch);
            $dispatcher->notify(new \Ip\Event($site, 'cron.afterFakeCron', $fakeCronAnswer));

        }


        \Db::disconnect();
        $dispatcher->notify(new \Ip\Event($site, 'site.databaseDisconnect', null));
    }
}