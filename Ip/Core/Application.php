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



    public function handleRequest(\Ip\Internal\Request $request)
    {
        global $site;
        \Ip\ServiceLocator::addRequest($request);
        $response = new \Ip\Response();

        $request->fixMagicQuotes();

        $language = ipGetCurrentLanguage();
        $languageCode = $language->getCode();

        \Ip\Translator::init($languageCode);
        \Ip\Translator::addTranslationFilePattern('phparray', \ip\Config::getCore('CORE_DIR') . 'Ip/languages', 'ipAdmin-%s.php', 'ipAdmin');
        \Ip\Translator::addTranslationFilePattern('phparray', \ip\Config::getCore('CORE_DIR') . 'Ip/languages', 'ipPublic-%s.php', 'ipPublic');

        //$this->modulesInit();
        \Ip\ServiceLocator::getDispatcher()->notify(new \Ip\Event($site, 'site.afterInit', null));


        $application = \Ip\ServiceLocator::getApplication();
        if ($request->isPost() && ($request->getPost('securityToken') !=  $application->getSecurityToken()) && empty($_POST['pa'])) {
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
            $response->addHeader('Content-type: text/json; charset=utf-8');
            $response->setContent(json_encode($data));
            \Ip\ServiceLocator::removeRequest();
            return $response;
        }






        if (sizeof($request->getRequest()) > 0) {

            $actionString = '';
            if(isset($_REQUEST['aa'])) {
                $actionString = $_REQUEST['aa'];
                $controllerClass = 'AdminController';
            } elseif(isset($_REQUEST['sa'])) {
                $actionString = $_REQUEST['sa'];
                $controllerClass = 'SiteController';
            } elseif(isset($_REQUEST['pa'])) {
                $actionString = $_REQUEST['pa'];
                $controllerClass = 'PublicController';
            }

            if ($actionString) {
                $parts = explode('.', $actionString);
                $module = array_shift($parts);
                if (isset($parts[0])) {
                    $action = $parts[0];
                } else {
                    $action = 'index';
                }
                //check if user is logged in
                if (isset($_REQUEST['aa']) && !\Ip\Backend::userId()) {
                    header('location: ' . \Ip\Config::baseUrl('') . 'admin');
                    exit;
                }
                //TODOX check if user has access to given module


                if (in_array($module, \Ip\Module\Plugins\Model::getModules())) {
                    $controllerClass = 'Ip\\Module\\'.$module.'\\'.$controllerClass;
                } else {
                    $controllerClass = 'Plugin\\'.$module.'\\'.$controllerClass;
                }
                if (!class_exists($controllerClass)) {
                    throw new \Ip\CoreException('Requested controller doesn\'t exist. '.$controllerClass);
                }
                $controller = new $controllerClass();
                $site->setLayout(\Ip\Config::getCore('CORE_DIR') . 'Ip/Module/Admin/View/layout.php');
                $site->addCss(\Ip\Config::libraryUrl('css/bootstrap/bootstrap.css'  ));
                $site->addJavascript(\Ip\Config::libraryUrl('css/bootstrap/bootstrap.js'));

                $answer = $controller->$action();

                if (is_string($answer)) {
                    $this->setBlockContent('main', $answer);
                } elseif ($answer instanceof \Ip\Response) {
                    \Ip\ServiceLocator::removeRequest();
                    return $answer;
                } elseif ($response === NULL) {
                    \Ip\ServiceLocator::removeRequest();
                    return $response;
                } else {
                    throw new \Ip\CoreException('Unknown response');
                }
            }


        }

        \Ip\ServiceLocator::removeRequest();
        return $response;

//        $response =  $site->generateOutput();




    }

    public function run()
    {
        $request = new \Ip\Internal\Request();
        $request->setGet($_GET);
        $request->setPost($_POST);
        $request->setServer($_SERVER);
        $request->setRequest($_REQUEST);
        $response = $this->handleRequest($request);
        $this->handleResponse($response);
        $this->close();
    }

    /**
     * @param \Ip\Response $response
     * @throws \Ip\CoreException
     */
    public function handleResponse(\Ip\Response $response)
    {
        $response->send();
    }

    public function close()
    {
        $dispatcher = \Ip\ServiceLocator::getDispatcher();
        global $site;

        /*
         Automatic execution of cron.
         The best solution is to setup cron service to launch file www.yoursite.com/ip_cron.php few times a day.
         By default fake cron is enabled
        */
        if(!\Ip\Module\Admin\Model::isSafeMode() && ipGetOption('Config.automaticCron', 1) && function_exists('curl_init') && \Ip\ServiceLocator::getLog()->lastLogsCount(60, 'system/cron') == 0){
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