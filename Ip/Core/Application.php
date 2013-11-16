<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Core;


class Application {
    protected $configPath = null;

    public function __construct($configPath)
    {
        $this->configPath = $configPath;
    }

    public static function getVersion()
    {
        return '4.0';
    }

    public function init()
    {
        $config = require ($this->configPath);
        require_once $config['BASE_DIR'] . $config['CORE_DIR'] . 'Ip/Config.php';
        \Ip\Config::init($config);

        require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/Autoloader.php';
        $autoloader = new \Ip\Autoloader();
        spl_autoload_register(array($autoloader, 'load'));



        require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/Sugar.php';
        require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/Internal/Deprecated/error_handler.php';
        require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/Internal/Deprecated/mysqlFunctions.php';

        global $parametersMod;
        $parametersMod = new \Ip\Internal\Deprecated\ParametersMod();

        if(session_id() == '' && !headers_sent()) { //if session hasn't been started yet
            session_name(\Ip\Config::getRaw('SESSION_NAME'));
            session_start();
        }



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
        \Ip\ServiceLocator::addRequest($request);


        $request->fixMagicQuotes();

        $language = ipGetCurrentLanguage();
        $languageCode = $language->getCode();

        \Ip\Translator::init($languageCode);
        \Ip\Translator::addTranslationFilePattern('phparray', \ip\Config::getCore('CORE_DIR') . 'Ip/languages', 'ipAdmin-%s.php', 'ipAdmin');
        \Ip\Translator::addTranslationFilePattern('phparray', \ip\Config::getCore('CORE_DIR') . 'Ip/languages', 'ipPublic-%s.php', 'ipPublic');

        $this->modulesInit();
        \Ip\ServiceLocator::getDispatcher()->notify(new \Ip\Event($this, 'site.afterInit', null));


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
            $response = new \Ip\Response();
            $response->addHeader('Content-type: text/json; charset=utf-8');
            $response->setContent(json_encode($data));
            \Ip\ServiceLocator::removeRequest();
            return $response;
        }


        $controllerClass = $request->getControllerClass();
        if (!class_exists($controllerClass)) {
            throw new \Ip\CoreException('Requested controller doesn\'t exist. '.$controllerClass);
        }

        $controller = new $controllerClass();

        //check if user is logged in
        if ($request->getControllerType() == \Ip\Internal\Request::CONTROLLER_TYPE_ADMIN && !\Ip\Backend::userId()) {
            //TODOX check if user has access to given module
            return new \Ip\Response\Redirect(\Ip\Config::baseUrl('') . 'admin');
        }



        $action = $request->getControllerAction();
        $controllerAnswer = $controller->$action();

        if (is_string($controllerAnswer) || $controllerAnswer instanceof \Ip\View) {
            if ($controllerAnswer instanceof \Ip\View) {
                $controllerAnswer = $controllerAnswer->render();
            }
            \Ip\ServiceLocator::getResponse()->setcontent($controllerAnswer);
            \Ip\ServiceLocator::removeRequest();
            return \Ip\ServiceLocator::getResponse();
        } elseif ($controllerAnswer instanceof \Ip\Response) {
            \Ip\ServiceLocator::removeRequest();
            return $controllerAnswer;
        } elseif ($controllerAnswer === NULL) {
            $response = \Ip\ServiceLocator::getResponse();
            \Ip\ServiceLocator::removeRequest();
            return $response;
        } else {
            throw new \Ip\CoreException('Unknown response');
        }

    }


    public function modulesInit(){
        //init core modules
        $coreModules = \Ip\Module\Plugins\Model::getModules();
        foreach($coreModules as $module) {
            $systemClass = '\\Ip\\Module\\'.$module.'\\System';
            if(class_exists($systemClass)) {
                $system = new $systemClass();
                if (method_exists($system, 'init')) {
                    $system->init();
                }
            }
        }
        //TODOX init plugins
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
            $dispatcher->notify(new \Ip\Event($this, 'cron.afterFakeCron', $fakeCronAnswer));
        }

        \Ip\Internal\Deprecated\Db::disconnect();
        $dispatcher->notify(new \Ip\Event($this, 'site.databaseDisconnect', null));
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