<?php
/**
 * @package   ImpressPages
 */

namespace Ip;


class Application {
    const ASSET_DIR = 'assets';
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

        $coreDir = !empty($config['CORE_DIR']) ? $config['CORE_DIR'] : dirname(__DIR__) . '/';

        require_once $coreDir . '/Ip/Config.php';

        $config = new \Ip\Config($config);
        require_once(__DIR__ . '/ServiceLocator.php');
        \Ip\ServiceLocator::setConfig($config);

        require_once $coreDir . 'Ip/Internal/Autoloader.php';

        $autoloader = new \Ip\Autoloader();
        spl_autoload_register(array($autoloader, 'load'));

        require_once $coreDir . 'Ip/Functions.php';

        require_once ipFile('Ip/Internal/Deprecated/mysqlFunctions.php');

        global $parametersMod;
        $parametersMod = new \Ip\Internal\Deprecated\ParametersMod();
    }

    public function prepareEnvironment($options = array())
    {
        //TODOX decide if separate option for error setting in config is needed
        if (empty($options['skipErrorHandler'])) {
            require_once ipFile('Ip/Internal/Deprecated/error_handler.php');
        }

        if (empty($options['skipError'])) {
            if (ipConfig()->isDevelopmentEnvironment()){
                error_reporting(E_ALL|E_STRICT);
                ini_set('display_errors', '1');
            } else {
                ini_set('display_errors', '0');
            }
        }

        if (empty($options['skipSession'])) {
            if(session_id() == '' && !headers_sent()) { //if session hasn't been started yet
                session_name(ipConfig()->getRaw('SESSION_NAME'));
                session_start();
            }
        }

        if (empty($options['skipEncoding'])) {
            mb_internal_encoding(ipConfig()->getRaw('CHARSET'));
        }

        if (empty($options['skipTimezone'])) {
            date_default_timezone_set(ipConfig()->getRaw('timezone')); //PHP 5 requires timezone to be set.
        }
    }


    protected function initTranslations($languageCode)
    {
        \Ip\Translator::init($languageCode);
        \Ip\Translator::addTranslationFilePattern('phparray', ipFile('Ip/languages'), 'ipAdmin-%s.php', 'ipAdmin');
        \Ip\Translator::addTranslationFilePattern('phparray', ipFile('Ip/languages'), 'ipPublic-%s.php', 'ipPublic');
    }

    /**
     * @param Request $request
     * @param bool $subrequest
     * @return Response
     * @throws CoreException
     */
    public function handleRequest(\Ip\Request $request, $options = array(), $subrequest = true)
    {

        \Ip\ServiceLocator::addRequest($request);
        if (!$subrequest) { // Do not fix magic quotes for internal requests because php didn't touched it
            $request->fixMagicQuotes();
        }

        if (empty($options['skipTranslationsInit'])) {
            if (!empty($options['translationsLanguageCode'])) {
                $languageCode = $options['translationsLanguageCode'];
            } else {
                $language = ipContent()->getCurrentLanguage();
                $languageCode = $language->getCode();
            }
            $this->initTranslations($languageCode);
        }

        if (empty($options['skipModuleInit'])) {
            $this->modulesInit();
        }
        ipDispatcher()->notify('site.afterInit');

        //check for CSRF attach
        if (empty($options['skipCsrfCheck']) && $request->isPost() && ($request->getPost('securityToken') !=  $this->getSecurityToken()) && empty($_POST['pa'])) {

            ipLog()->error('Core.possibleCsrfAttack', array('post' => ipRequest()->getPost()));
            $data = array(
                'status' => 'error'
            );
            if (ipConfig()->isDevelopmentEnvironment()) {
                $data['errors'] = array(
                    'securityToken' => __('Possible CSRF attack. Please pass correct securityToken.', 'ipAdmin')
                );
            }
            // TODO JSONRPC
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

        //check if user is logged in
        if ($request->getControllerType() == \Ip\Request::CONTROLLER_TYPE_ADMIN && !\Ip\Module\Admin\Backend::userId()) {
            //TODOX check if user has access to given module
            return new \Ip\Response\Redirect(ipConfig()->baseUrl() . 'admin');
        }

        $action = $request->getControllerAction();



        $controller = new $controllerClass();
        if (!$controller instanceof \Ip\Controller) {
            throw new \Ip\CoreException($controllerClass.".php must extend \\Ip\\Controller class.");
        }
        $controller->init();
        $controllerAnswer = $controller->$action();

        if (empty($controllerAnswer) || is_string($controllerAnswer) || $controllerAnswer instanceof \Ip\View) {
            if ($controllerAnswer instanceof \Ip\View) {
                $controllerAnswer = $controllerAnswer->render();
            }
            if (empty($controllerAnswer)) {
                $controllerAnswer = '';
            }
            \Ip\ServiceLocator::response()->setContent($controllerAnswer);
            \Ip\ServiceLocator::removeRequest();
            return \Ip\ServiceLocator::response();
        } elseif ($controllerAnswer instanceof \Ip\Response) {
            \Ip\ServiceLocator::removeRequest();
            \Ip\ServiceLocator::setResponse($controllerAnswer);
            return $controllerAnswer;
        } elseif ($controllerAnswer === NULL) {
            $response = \Ip\ServiceLocator::response();
            \Ip\ServiceLocator::removeRequest();
            return $response;
        } else {
            throw new \Ip\CoreException('Unknown response');
        }

    }


    public function modulesInit(){
        //init core modules

        //TODO hardcode system modules
        $coreModules = \Ip\Module\Plugins\Model::getModules();
        foreach ($coreModules as $module) {
            $systemClass = '\\Ip\\Module\\'.$module.'\\System';
            if(class_exists($systemClass)) {
                $system = new $systemClass();
                if (method_exists($system, 'init')) {
                    $system->init();
                }
            }
        }

        $plugins = \Ip\Module\Plugins\Model::getActivePlugins();
        foreach ($plugins as $plugin) {
            $systemClass = '\\Plugin\\' . $plugin . '\\System';
            if (class_exists($systemClass)) {
                $system = new $systemClass();
                if (method_exists($system, 'init')) {
                    $system->init();
                }
            }
        }

    }


    public function run($options = array())
    {
        $this->prepareEnvironment($options);
        $request = new \Ip\Request();
        $request->setGet($_GET);
        $request->setPost($_POST);
        $request->setServer($_SERVER);
        $request->setRequest($_REQUEST);



        $response = $this->handleRequest($request, $options, false);
        $this->handleResponse($response);
        $this->close();
    }

    /**
     * @param \Ip\Response $response
     * @throws \Ip\CoreException
     */
    public function handleResponse(\Ip\Response $response)
    {
        $response = ipDispatcher()->filter('Application.sendResponse', $response);
        ipDispatcher()->notify('Application.sendResponse', array('response' => $response));
        $response->send();
    }

    public function close()
    {
        /*
         Automatic execution of cron.
         The best solution is to setup cron service to launch file www.yoursite.com/ip_cron.php few times a day.
         By default fake cron is enabled
        */
        if (!\Ip\Module\Admin\Model::isSafeMode() && ipGetOption('Config.automaticCron', 1)) {
            $lastExecution = \Ip\ServiceLocator::storage()->get('Cron', 'lastExecutionStart');
            if (!$lastExecution || date('Y-m-d H') != date('Y-m-d H', $lastExecution)) { // Execute Cron once an hour

                // create a new curl resource
                if (function_exists('curl_init')) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, ipConfig()->baseUrl() . '?pa=Cron&pass=' . urlencode(ipGetOption('Config.cronPassword')));
                    curl_setopt($ch, CURLOPT_REFERER, ipConfig()->baseUrl());
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                    $fakeCronAnswer = curl_exec($ch);
                } else {
                    $request = new \Ip\Request();
                    $request->setGet(array(
                            'pa' => 'Cron',
                            'pass' => ipGetOption('Config.cronPassword')
                    ));
                    $fakeCronAnswer = $this->handleRequest($request)->getContent();
                }

                if ($fakeCronAnswer != __('OK', 'ipAdmin', false)) {
                    ipLog()->error('Cron.failedFakeCron', array('result' => $fakeCronAnswer));
                }
            }

        }

        ipDb()->disconnect();
        ipDispatcher()->notify('site.databaseDisconnect');
    }

    /**
     * Get security token used to prevent cross site scripting
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