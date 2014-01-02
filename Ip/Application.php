<?php
/**
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
        $originalDir = ipFile("file/translations/original/");
        $overrideDir = ipFile("file/translations/override/");

        $translator->addTranslationFilePattern(
            'json',
            $originalDir,
            "%s/theme-$theme.json",
            'theme-' . ipConfig()->theme()
        );
        $translator->addTranslationFilePattern(
            'json',
            ipFile("Theme/$theme/translations/"),
            "%s/theme-$theme.json",
            'theme-' . ipConfig()->theme()
        );
        $translator->addTranslationFilePattern(
            'json',
            $overrideDir,
            "%s/theme-$theme.json",
            'theme-' . ipConfig()->theme()
        );

        $translator->addTranslationFilePattern('json', $originalDir, "%s/ipAdmin.json", 'ipAdmin');
        $translator->addTranslationFilePattern(
            'json',
            ipFile("Ip/Translator/translations/"),
            "%s/ipAdmin.json",
            'ipAdmin'
        );
        $translator->addTranslationFilePattern('json', $overrideDir, "%s/ipAdmin.json", 'ipAdmin');

        $translator->addTranslationFilePattern('json', $originalDir, "%s/ipPublic.json", 'ipPublic');
        $translator->addTranslationFilePattern(
            'json',
            ipFile("Ip/Translator/translations/"),
            "%s/ipPublic.json",
            'ipPublic'
        );
        $translator->addTranslationFilePattern('json', $overrideDir, "%s/ipPublic.json", 'ipPublic');
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
                $languageCode = ipContent()->getCurrentLanguage()->getCode();
            }
            $this->initTranslations($languageCode);
        }

        if (empty($options['skipModuleInit'])) {
            $this->modulesInit();
        }
        ipDispatcher()->notify('site.afterInit');

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
            $response = new \Ip\Response();
            $response->addHeader('Content-type: text/json; charset=utf-8');
            $response->setContent(json_encode($data));
            \Ip\ServiceLocator::removeRequest();
            return $response;
        }


        $controllerClass = $request->getControllerClass();
        if (!class_exists($controllerClass)) {
            throw new \Ip\CoreException('Requested controller doesn\'t exist. ' . $controllerClass);
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
            if (!ipIsAllowed($plugin, 'executeAdminAction', array('action' => $action))) {
                throw new \Ip\CoreException('User has no permission to execute ' . $request->getControllerModule(
                    ) . '.' . $request->getControllerAction() . ' action');
            }
        }

        $controller = new $controllerClass();
        if (!$controller instanceof \Ip\Controller) {
            throw new \Ip\CoreException($controllerClass . ".php must extend \\Ip\\Controller class.");
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
        } elseif ($controllerAnswer === null) {
            $response = \Ip\ServiceLocator::response();
            \Ip\ServiceLocator::removeRequest();
            return $response;
        } else {
            throw new \Ip\CoreException('Unknown response');
        }

    }


    public function modulesInit()
    {
        //init core modules

        //TODO hardcode system modules
        $coreModules = \Ip\Internal\Plugins\Model::getModules();
        foreach ($coreModules as $module) {
            $systemClass = '\\Ip\\Internal\\' . $module . '\\System';
            if (class_exists($systemClass)) {
                $system = new $systemClass();
                if (method_exists($system, 'init')) {
                    $system->init();
                }
            }
        }

        $translator = \Ip\ServiceLocator::translator();
        $originalDir = ipFile("file/translations/original/");
        $overrideDir = ipFile("file/translations/override/");

        $plugins = \Ip\Internal\Plugins\Model::getActivePlugins();
        foreach ($plugins as $plugin) {

            $translator->addTranslationFilePattern(
                'json',
                $originalDir,
                "%s/$plugin.json",
                $plugin
            );
            $translator->addTranslationFilePattern(
                'json',
                ipFile("Plugin/$plugin/translations/"),
                "%s.json",
                $plugin
            );
            $translator->addTranslationFilePattern(
                'json',
                $overrideDir,
                "%s/$plugin.json",
                $plugin
            );

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
        ipDispatcher()->notify('Application.close');

        ipDb()->disconnect();
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