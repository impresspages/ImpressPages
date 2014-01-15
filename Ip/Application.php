<?php
/**
 * Base class for ImpressPages CMS application.
 *
 * @package   ImpressPages
 */

namespace Ip;


class Application
{
    const ASSETS_DIR = 'assets';
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
                session_name(ipConfig()->getRaw('SESSION_NAME'));
                session_start();
            }
        }

        if (empty($options['skipEncoding'])) {
            mb_internal_encoding(ipConfig()->getRaw('CHARSET'));
        }

        if (empty($options['skipTimezone'])) {
            date_default_timezone_set(ipConfig()->getRaw('TIMEZONE')); //PHP 5 requires timezone to be set.
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

    private function handleOnlyRequest(\Ip\Request $request, $options = array(), $subrequest = true)
    {
        if (empty($options['skipInitEvents'])) {
            \Ip\ServiceLocator::dispatcher()->_bindApplicationEvents();
        }

        if (!$subrequest) { // Do not fix magic quotes for internal requests because php didn't touched it
            $request->fixMagicQuotes();
        }

        if (empty($options['skipTranslationsInit'])) {
            if (!empty($options['translationsLanguageCode'])) {
                $languageCode = $options['translationsLanguageCode'];
            } else {
                $languageCode = ipContent()->getCurrentLanguage()->getCode();
            }
            $this->initTranslations($languageCode);
        }

        if (empty($options['skipModuleInit'])) {
            $this->modulesInit();
        }
        ipEvent('ipInitFinished');

        //check for CSRF attack
        if (empty($options['skipCsrfCheck']) && $request->isPost() && ($request->getPost(
                    'securityToken'
                ) != $this->getSecurityToken()) && empty($_POST['pa'])
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


        $controllerClass = $request->getControllerClass();
        if (!class_exists($controllerClass)) {
            throw new \Ip\Exception('Requested controller doesn\'t exist. ' . $controllerClass);
        }

        //check if user is logged in
        if ($request->getControllerType() == \Ip\Request::CONTROLLER_TYPE_ADMIN && !\Ip\Internal\Admin\Backend::userId(
            )
        ) {

            if (ipConfig()->getRaw('NO_REWRITES')) {
                return new \Ip\Response\Redirect(ipConfig()->baseUrl() . 'index.php/admin');
            } else {
                return new \Ip\Response\Redirect(ipConfig()->baseUrl() . 'admin');
            }
        }

        $action = $request->getControllerAction();

        if ($request->getControllerType() == \Ip\Request::CONTROLLER_TYPE_ADMIN) {
            $plugin = $request->getControllerModule();
            if (!ipAdminPermission($plugin, 'executeAdminAction', array('action' => $action))) {
                throw new \Ip\Exception('User has no permission to execute ' . $request->getControllerModule(
                    ) . '.' . $request->getControllerAction() . ' action');
            }
        }

        $controller = new $controllerClass();
        if (!$controller instanceof \Ip\Controller) {
            throw new \Ip\Exception($controllerClass . ".php must extend \\Ip\\Controller class.");
        }
        $controller->init();
        $controllerAnswer = $controller->$action();

        return $controllerAnswer;
    }

    /**
     * @param Request $request
     * @param bool $subrequest
     * @return Response
     * @throws Exception
     */
    public function handleRequest(\Ip\Request $request, $options = array(), $subrequest = true)
    {

        \Ip\ServiceLocator::addRequest($request);

        $rawResponse = $this->handleOnlyRequest($request, $options, $subrequest);

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


    public function modulesInit()
    {
        ipEvent('ipInit');

        $translator = \Ip\ServiceLocator::translator();
        $originalDir = ipFile("file/translations/original/");
        $overrideDir = ipFile("file/translations/override/");

        $plugins = \Ip\Internal\Plugins\Model::getActivePlugins();
        foreach ($plugins as $plugin) {

            $translationsDir = ipFile("Plugin/$plugin/translations/");
            $translator->addTranslationFilePattern('json', $originalDir,        "$plugin-%s.json", $plugin);
            $translator->addTranslationFilePattern('json', $translationsDir,    "$plugin-%s.json", $plugin);
            $translator->addTranslationFilePattern('json', $overrideDir,        "$plugin-%s.json", $plugin);
        }

    }


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
     * @param \Ip\Response $response
     * @throws \Ip\Exception
     */
    public function handleResponse(\Ip\Response $response)
    {
        $response = ipFilter('ipSendResponse', $response);
        ipEvent('ipBeforeResponseSent', array('response' => $response));
        $response->send();
    }

    public function close()
    {
        ipEvent('ipBeforeApplicationClosed');

        ipDb()->disconnect();
    }

    /**
     * Get security token used to prevent cross site scripting
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