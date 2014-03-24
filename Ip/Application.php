<?php
/**
 * @package   ImpressPages
 */

namespace Ip;

/**
 * Base class for ImpressPages application
 */


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
        return '4.0.3'; //CHANGE_ON_VERSION_UPDATE
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
                if (!ipConfig()->getRaw('disableHttpOnlySetting')) {
                    ini_set('session.cookie_httponly', 1);
                }

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

        $translator->addTranslationFilePattern('json', $originalDir,    'Ip-admin-%s.json', 'Ip-admin');
        $translator->addTranslationFilePattern('json', $ipDir,          'Ip-admin-%s.json', 'Ip-admin');
        $translator->addTranslationFilePattern('json', $overrideDir,    'Ip-admin-%s.json', 'Ip-admin');

        $translator->addTranslationFilePattern('json', $originalDir,    'Ip-%s.json', 'Ip');
        $translator->addTranslationFilePattern('json', $ipDir,          'Ip-%s.json', 'Ip');
        $translator->addTranslationFilePattern('json', $overrideDir,    'Ip-%s.json', 'Ip');
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
        if ($result) {
            $requestLanguage = $result['language'];
            $routeLanguage = $requestLanguage->getCode();
            $relativeUri = $result['relativeUri'];
        } else {
            $routeLanguage = null;
            $requestLanguage = ipJob('ipRequestLanguage', array('request' => $request));
            $relativeUri = $request->getRelativePath();
        }

        ipContent()->_setCurrentLanguage($requestLanguage);

        $_SESSION['ipLastLanguageId'] = $requestLanguage->getId();

        if (empty($options['skipTranslationsInit'])) {
            if (!empty($options['translationsLanguageCode'])) {
                $languageCode = $options['translationsLanguageCode'];
            } else {
                $languageCode = $requestLanguage->getCode();
            }
            $this->initTranslations($languageCode);
        }

        $routeAction = ipJob('ipRouteAction', array('request' => $request, 'relativeUri' => $relativeUri, 'routeLanguage' => $routeLanguage));

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
                    'securityToken' => __('Possible CSRF attack. Please pass correct securityToken.', 'Ip-admin')
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

        $eventInfo = $routeAction;

        if (!empty($routeAction['plugin'])) {

            $plugin = $routeAction['plugin'];
            $controller = $routeAction['controller'];

            if (in_array($plugin, \Ip\Internal\Plugins\Model::getModules())) {
                $controllerClass = 'Ip\\Internal\\'.$plugin.'\\'.$controller;
            } else {
                if (!in_array($plugin, \Ip\Internal\Plugins\Service::getActivePluginNames())) {
                    throw new \Ip\Exception("Plugin '" . esc($plugin) . "' doesn't exist or isn't activated.");
                }
                $controllerClass = 'Plugin\\'.$plugin.'\\'.$controller;
            }

            if (!class_exists($controllerClass)) {
                throw new \Ip\Exception('Requested controller doesn\'t exist. ' . esc($controllerClass));
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
                if (!ipAdminPermission($plugin)) {
                    throw new \Ip\Exception('User has no permission to access ' . esc($plugin) . '');
                }
            }

            $eventInfo['controllerClass'] = $controllerClass;
            $eventInfo['controllerType'] = $controller;
        }

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
