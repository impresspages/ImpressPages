<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Core;


class Application {

    public static function init()
    {
        if (!defined('IP_VERSION')) {
            define('IP_VERSION', '4.0');
        }

        require_once \Ip\Config::includePath('parameters.php');

        require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/Site.php';
        require_once \Ip\Config::includePath('error_handler.php');

        if(!\Ip\Deprecated\Db::connect()){
            trigger_error("Database access");
        }

        global $log;
        $log = new \Ip\Module\Log\Module();
        global $dispatcher;
        $dispatcher = new \Ip\Dispatcher();
        global $parametersMod;
        $parametersMod = new \parametersMod();
        global $session;
        $session = new \Ip\Frontend\Session();
        global $site;
        $site = new \Site();

        mb_internal_encoding(\Ip\Config::getRaw('CHARSET'));
        date_default_timezone_set(\Ip\Config::getRaw('timezone')); //PHP 5 requires timezone to be set.

        if (\Ip\Config::isDevelopmentEnvironment()){
            error_reporting(E_ALL|E_STRICT);
            ini_set('display_errors', '1');
        } else {
            ini_set('display_errors', '0');
        }
    }

    public function __construct()
    {
    }

    public function handleRequest()
    {
        global $site, $parametersMod, $dispatcher;

        \Ip\Response::reset();

        $site->init();
        $site->dispatchEvent('administrator', 'system', 'init', array());
        $dispatcher->notify(new \Ip\Event($site, 'site.afterInit', null));

        /*detect browser language*/
        if((!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == '') && $parametersMod->getValue('standard', 'languages', 'options', 'detect_browser_language') && $site->getCurrentUrl() == \Ip\Config::baseUrl('') && !isset($_SESSION['modules']['standard']['languages']['language_selected_by_browser']) && $parametersMod->getValue('standard', 'languages', 'options', 'multilingual')){
            require_once \Ip\Config::libraryFile('php/browser_detection/language.php');

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

        $language = $site->getCurrentLanguage();
        $languageCode = $language->getCode();

        \Ip\Translator::init($languageCode . '_' . strtoupper($languageCode));

        /*check if the website is closed*/
        if($parametersMod->getValue('standard', 'configuration', 'main_parameters', 'closed_site') && !$site->managementState()
            && (!\Ip\Backend::loggedIn() || !isset($_REQUEST['g']) || !isset($_REQUEST['m']) || !isset($_REQUEST['a']))){
            return $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'closed_site_message');
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

                \Ip\Response::header('Content-type: text/json; charset=utf-8'); //throws save file dialog on firefox if iframe is used
                return json_encode($data);
            }


            $site->makeActions(); //all posts are handled by "site" and redirected to current module actions.php before any output.


            if (!$site->managementState() && !\Ip\Module\Design\ConfigModel::instance()->isInPreviewState()) {
                $site->makeRedirect(); //if required;
            }
        }

        return $site->generateOutput();
    }

    public function run()
    {
        $response = $this->handleRequest();
        $this->handleResponse($response);
        $this->close();
    }

    public function handleResponse($response)
    {
        if (is_string($response)) {
            // global $dispather, $site;
            // $dispatcher->notify(new \Ip\Event($site, 'site.outputGenerated', array('output' => &$response)));
            echo $response;
            // $dispatcher->notify(new \Ip\Event($site, 'site.outputPrinted', array('output' => &$response)));
        } elseif (is_a($response, '\Ip\View')) {
            echo $response->render();
        }
    }

    public function close()
    {
        global $dispatcher, $site, $log, $parametersMod;

        /*
         Automatic execution of cron.
         The best solution is to setup cron service to launch file www.yoursite.com/ip_cron.php few times a day.
         By default fake cron is enabled
        */
        if(!\Ip\Module\Admin\Model::isSafeMode() && $parametersMod->getValue('standard', 'configuration', 'advanced_options', 'use_fake_cron') && function_exists('curl_init') && $log->lastLogsCount(60, 'system/cron') == 0){
            // create a new curl resource

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, \Ip\Config::baseUrl('ip_cron.php'));
            curl_setopt($ch, CURLOPT_REFERER, \Ip\Config::baseUrl(''));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            $fakeCronAnswer = curl_exec($ch);
            $dispatcher->notify(new \Ip\Event($site, 'cron.afterFakeCron', $fakeCronAnswer));
        }

        \Ip\Deprecated\Db::disconnect();
        $dispatcher->notify(new \Ip\Event($site, 'site.databaseDisconnect', null));
    }
}