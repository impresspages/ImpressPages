<?php
/**
 * Base class for ImpressPages application.
 *
 * @package   ImpressPages
 */

namespace Ip;


class Application
{
    const ASSETS_DIR = 'assets';
    protected $configPath = null;

    /**
     * @ignore
     * @param $configPath
     */
    public function __construct($configPath)
    {
        $this->configPath = $configPath;
    }

    /**
     * Get framework version
     * @return string
     */
    public static function getVersion()
    {
        return '4.0.0';
    }

    /**
     * @ignore
     */
    public function init()
    {
        $config = require($this->configPath);

        $coreDir = !empty($config['CORE_DIR']) ? $config['CORE_DIR'] : dirname(__DIR__) . '/';

        require_once $coreDir . 'Ip/Config.php';

        $config = new \Ip\Config($config);
        require_once $coreDir . 'Ip/ServiceLocator.php';
        \Ip\ServiceLocator::setConfig($config);

        require_once $coreDir . 'Ip/Internal/Autoloader.php';

        $autoloader = new \Ip\Autoloader();
        spl_autoload_register(array($autoloader, 'load'));

        require_once $coreDir . 'Ip/Functions.php';
    }

    /**
     * @ignore
     * @param array $options
     */
    public function prepareEnvironment($options = array())
    {
        if (empty($options['skipErrorHandler'])) {
            set_error_handler(array('Ip\Internal\ErrorHandler', 'ipErrorHandler'));
        }

        if (empty($options['skipError'])) {
            if (ipConfig()->isDevelopmentEnvironment()) {
                error_reporting(E_ALL | E_STRICT);
                ini_set('display_errors', '1');
            } else {
                ini_set('display_errors', '0');
            }
        }

        if (empty($options['skipSession'])) {
            if (session_id() == '' && !headers_sent()) { //if session hasn't been started yet
                session_name(ipConfig()->getRaw('sessionName'));
                session_start();
            }
        }

        if (empty($options['skipEncoding'])) {
            mb_internal_encoding(ipConfig()->getRaw('charset'));
        }

        if (empty($options['skipTimezone'])) {
            date_default_timezone_set(ipConfig()->getRaw('timezone')); //PHP 5 requires timezone to be set.
        }
    }


    protected function initTranslations($languageCode)
    {
        $translator = \Ip\ServiceLocator::translator();
        $translator->setLocale($languageCode);

        $theme = ipConfig()->theme();
        $originalDir = ipFile('file/translations/original/');
        $overrideDir = ipFile('file/translations/override/');
        $themeDir = ipFile("Theme/$theme/translations/");
        $ipDir = ipFile('Ip/Internal/Translations/translations/');

        $translator->addTranslationFilePattern('json', $originalDir,    "$theme-%s.json", $theme);
        $translator->addTranslationFilePattern('json', $themeDir,       "$theme-%s.json", $theme);
        $translator->addTranslationFilePattern('json', $overrideDir,    "$theme-%s.json", $theme);

        $translator->addTranslationFilePattern('json', $originalDir,    'ipAdmin-%s.json', 'ipAdmin');
        $translator->addTranslationFilePattern('json', $ipDir,          'ipAdmin-%s.json', 'ipAdmin');
        $translator->addTranslationFilePattern('json', $overrideDir,    'ipAdmin-%s.json', 'ipAdmin');

        $translator->addTranslationFilePattern('json', $originalDir,    'ipPublic-%s.json', 'ipPublic');
        $translator->addTranslationFilePattern('json', $ipDir,          'ipPublic-%s.json', 'ipPublic');
        $translator->addTranslationFilePattern('json', $overrideDir,    'ipPublic-%s.json', 'ipPublic');
    }

    /**
     * @ignore
     * @param Request $request
     * @param array $options
     * @param bool $subrequest
     * @return Response\Json|Response\PageNotFound|Response\Redirect
     * @throws Exception
     * @ignore
     */

    public function _handleOnlyRequest(\Ip\Request $request, $options = array(), $subrequest = true)
    {
        if (empty($options['skipInitEvents'])) {
            \Ip\ServiceLocator::dispatcher()->_bindApplicationEvents();
        }

        if (!$subrequest) { // Do not fix magic quotes for internal requests because php didn't touched it
            $request->fixMagicQuotes();
        }

        $result = ipJob('ipRouteLanguage', array('request' => $request, 'relativeUri' => $request->getRelativePath()));
        $language = $result['language'];
        $relativeUri = $result['relativeUri'];

        ipContent()->_setCurrentLanguage($language);

        $_SESSION['ipLastLanguageId'] = $language->getId();

        if (empty($options['skipTranslationsInit'])) {
            if (!empty($options['translationsLanguageCode'])) {
                $languageCode = $options['translationsLanguageCode'];
            } else {
                $languageCode = $language->getCode();
            }
            $this->initTranslations($languageCode);
        }


        $routeAction = ipJob('ipRouteAction', array('request' => $request, 'relativeUri' => $relativeUri));

        if (!empty($routeAction)) {
            if (!empty($routeAction['page'])) {
                ipContent()->_setCurrentPage($routeAction['page']);
            }
        }

        if (empty($options['skipModuleInit'])) {
            $this->modulesInit();
        }
        ipEvent('ipInitFinished');



        //check for CSRF attack
        if (empty($options['skipCsrfCheck']) && $request->isPost() && ($request->getPost(
                    'securityToken'
                ) != $this->getSecurityToken()) && !$request->getPost('pa')
        ) {

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
            return new \Ip\Response\Json($data);
        }

        if (empty($routeAction)) {
            $routeAction = array(
                'plugin' => 'Core',
                'controller' => 'PublicController',
                'action' => 'pageNotFound'
            );
        }
        $plugin = $routeAction['plugin'];
        $controller = $routeAction['controller'];
        $action = $routeAction['action'];


        if (in_array($plugin, \Ip\Internal\Plugins\Model::getModules())) {
            $controllerClass = 'Ip\\Internal\\'.$plugin.'\\'.$controller;
        } else {
            if (!in_array($plugin, \Ip\Internal\Plugins\Service::getActivePluginNames())) {
                throw new \Ip\Exception("Plugin '".$plugin."' doesn't exist or isn't activated.");
            }
            $controllerClass = 'Plugin\\'.$plugin.'\\'.$controller;
        }

        if (!class_exists($controllerClass)) {
            throw new \Ip\Exception('Requested controller doesn\'t exist. ' . $controllerClass);
        }

        // check if user is logged in
        if ($controller == 'AdminController' && !\Ip\Internal\Admin\Backend::userId()) {

            if (ipConfig()->getRaw('rewritesDisabled')) {
                return new \Ip\Response\Redirect(ipConfig()->baseUrl() . 'index.php/admin');
            } else {
                return new \Ip\Response\Redirect(ipConfig()->baseUrl() . 'admin');
            }
        }

        if ($controller == 'AdminController') {
            if (!ipAdminPermission($plugin, 'executeAdminAction', array('action' => $action))) {
                throw new \Ip\Exception('User has no permission to execute ' . $plugin . '.' . $action . ' action');
            }
        }

        $eventInfo = $routeAction;

        $eventInfo['controllerClass'] = $controllerClass;
        $eventInfo['controllerType'] = $controller;
        if (empty($eventInfo['page'])) {
            $eventInfo['page'] = null;
        }

        ipEvent('ipBeforeController', $eventInfo);

        $controllerAnswer = ipJob('ipExecuteController', $eventInfo);

        return $controllerAnswer;
    }

    /**
     * Handle HMVC request
     * @param Request $request Request object with MVC query
     * @return Response
     * @throws Exception
     */
    public function handleRequest(\Ip\Request $request, $options = array(), $subrequest = true)
    {

        \Ip\ServiceLocator::addRequest($request);

        $rawResponse = $this->_handleOnlyRequest($request, $options, $subrequest);

        if (!empty($options['returnRawResponse'])) {
            return $rawResponse;
        }

        if (empty($rawResponse) || is_string($rawResponse) || $rawResponse instanceof \Ip\View) {
            if ($rawResponse instanceof \Ip\View) {
                $rawResponse = $rawResponse->render();
            }
            if (empty($rawResponse)) {
                $rawResponse = '';
            }

            $response = \Ip\ServiceLocator::response();
            $response->setContent($rawResponse);
        } elseif ($rawResponse instanceof \Ip\Response) {
            \Ip\ServiceLocator::setResponse($rawResponse);
            return $rawResponse;
        } elseif ($rawResponse === null) {
            $response = \Ip\ServiceLocator::response();
        } else {
            throw new \Ip\Exception('Unknown response');
        }

        if (method_exists($response, 'execute')) {
            $response = $response->execute();
        }

        \Ip\ServiceLocator::removeRequest();

        return $response;
    }

    /**
     * @ignore
     */
    public function modulesInit()
    {
        $translator = \Ip\ServiceLocator::translator();
        $originalDir = ipFile("file/translations/original/");
        $overrideDir = ipFile("file/translations/override/");

        $plugins = \Ip\Internal\Plugins\Service::getActivePluginNames();
        foreach ($plugins as $plugin) {

            $translationsDir = ipFile("Plugin/$plugin/translations/");
            $translator->addTranslationFilePattern('json', $originalDir,        "$plugin-%s.json", $plugin);
            $translator->addTranslationFilePattern('json', $translationsDir,    "$plugin-%s.json", $plugin);
            $translator->addTranslationFilePattern('json', $overrideDir,        "$plugin-%s.json", $plugin);
        }
    }

    /**
     * @ignore
     * @param array $options
     */
    public function run($options = array())
    {
        $this->prepareEnvironment($options);
        $request = new \Ip\Request();
        $request->setQuery($_GET);
        $request->setPost($_POST);
        $request->setServer($_SERVER);
        $request->setRequest($_REQUEST);


        $response = $this->handleRequest($request, $options, false);
        $this->handleResponse($response);
        $this->close();
    }

    /**
     * @ignore
     * @param \Ip\Response $response
     * @throws \Ip\Exception
     */
    public function handleResponse(\Ip\Response $response)
    {
        $response = ipFilter('ipSendResponse', $response);
        ipEvent('ipBeforeResponseSent', array('response' => $response));
        $response->send();
    }

    /**
     * @ignore
     */
    public function close()
    {
        ipEvent('ipBeforeApplicationClosed');

        ipDb()->disconnect();
    }

    /**
     * Get security token used to prevent cross site scripting attacks
     *
     * @return string security token
     */
    public function getSecurityToken()
    {
        if (empty($_SESSION['ipSecurityToken'])) {
            $_SESSION['ipSecurityToken'] = md5(uniqid(rand(), true));
        }
        return $_SESSION['ipSecurityToken'];
    }
}
