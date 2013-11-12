<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Core;


class Application {

    public static function init()
    {
        //TODOX remove constant. Constants are evil
        if (!defined('IP_VERSION')) {
            define('IP_VERSION', '4.0');
        }

        require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/Sugar.php';
        require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/Site.php';
        require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/Internal/Deprecated/error_handler.php';
        require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/Internal/Deprecated/mysqlFunctions.php';

        global $dispatcher;
        $dispatcher = new \Ip\Dispatcher();
        global $parametersMod;
        $parametersMod = new \Ip\Internal\Deprecated\ParametersMod();

        if(session_id() == '' && !headers_sent()) { //if session hasn't been started yet
            session_name(\Ip\Config::getRaw('SESSION_NAME'));
            session_start();
        }

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
        /*detect browser language*/
        if((!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == '') && $parametersMod->getValue('standard', 'languages', 'options', 'detect_browser_language') && $site->getCurrentUrl() == \Ip\Config::baseUrl('') && !isset($_SESSION['modules']['standard']['languages']['language_selected_by_browser']) && $parametersMod->getValue('standard', 'languages', 'options', 'multilingual')){
            require_once \Ip\Config::libraryFile('php/browser_detection/language.php');

            $browserLanguages = \Ip\Browser::getLanguages();
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

        \Ip\Translator::init($languageCode);
        \Ip\Translator::addTranslationFilePattern('phparray', \ip\Config::getCore('CORE_DIR') . 'Ip/languages', 'ipAdmin-%s.php', 'ipAdmin');
        \Ip\Translator::addTranslationFilePattern('phparray', \ip\Config::getCore('CORE_DIR') . 'Ip/languages', 'ipPublic-%s.php', 'ipPublic');

        $session = \Ip\ServiceLocator::getApplication();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
            (empty($_POST['securityToken']) || $_POST['securityToken'] !=  $session->getSecurityToken()) && empty($_POST['pa'])
        ) {
            $log = \Ip\ServiceLocator::getLog();
            $log->log('ImpressPages Core', 'CSRF check', 'Possible CSRF attack. ' . serialize(\Ip\ServiceLocator::getRequest()->getPost()));
            $data = array(
                'status' => 'error'
            );
            if (\Ip\Config::isDevelopmentEnvironment()) {
                $data['errors'] = array(
                    'securityToken' => __('Possible CSRF attack. Please pass correct securityToken.', 'ipAdmin')
                );
            }

            \Ip\Response::header('Content-type: text/json; charset=utf-8'); //throws save file dialog on firefox if iframe is used
            return json_encode($data);
        }

        $site->modulesInit();
        $dispatcher->notify(new \Ip\Event($site, 'site.afterInit', null));




        $site->makeActions(); //all posts are handled by "site" and redirected to current module actions.php before any output.


        if (!$site->managementState() && !\Ip\Module\Design\ConfigModel::instance()->isInPreviewState()) {
            $site->makeRedirect(); //if required;
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
        } elseif ($response instanceof \Ip\Response\ResponseInterface) {
            $response->send();
        } elseif ($response === NULL) {
            // TODOX should we do something
        } else {
            throw new \Ip\CoreException('Unknown response');
        }
    }

    public function close()
    {
        global $dispatcher, $site, $log;

        /*
         Automatic execution of cron.
         The best solution is to setup cron service to launch file www.yoursite.com/ip_cron.php few times a day.
         By default fake cron is enabled
        */
        if(!\Ip\Module\Admin\Model::isSafeMode() && ipGetOption('Config.automaticCron', 1) && function_exists('curl_init') && $log->lastLogsCount(60, 'system/cron') == 0){
            // create a new curl resource

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, \Ip\Config::baseUrl('ip_cron.php'));
            curl_setopt($ch, CURLOPT_REFERER, \Ip\Config::baseUrl(''));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            $fakeCronAnswer = curl_exec($ch);
            $dispatcher->notify(new \Ip\Event($site, 'cron.afterFakeCron', $fakeCronAnswer));
        }

        \Ip\Internal\Deprecated\Db::disconnect();
        $dispatcher->notify(new \Ip\Event($site, 'site.databaseDisconnect', null));
    }

    /**
     * Get security token used to prevent cros site scripting
     * @return string
     */
    public function getSecurityToken()
    {
        if (empty($_SESSION['ipSecurityToken'])) {
            $_SESSION['ipSecurityToken'] = md5(uniqid(rand(), true));
        }
        return $_SESSION['ipSecurityToken'];
    }
}