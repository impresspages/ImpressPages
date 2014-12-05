<?php

/**
 * @package ImpressPages
 */

if ((PHP_MAJOR_VERSION < 5) || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 3)) {
    echo 'Your PHP version is: ' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '. To run ImpressPages you need PHP >= 5.3.*';
    exit;
}

require_once(__DIR__ . '/../Ip/Application.php');


$application = new \Ip\Application(__DIR__ . '/config.php');
$application->init();
$options = array(
    'skipErrorHandler' => true
);
$application->prepareEnvironment($options);
$options = array(
    'skipInitEvents' => true,
    'skipModuleInit' => true,
    'translationsLanguageCode' => \Plugin\Install\Helper::$defaultLanguageCode
);

if (!empty($_REQUEST['lang']) && strlen($_REQUEST['lang']) == 2 && ctype_alpha($_REQUEST['lang'])) {
    $_SESSION['installationLanguage'] = $_REQUEST['lang'];
}

if (isset($_SESSION['installationLanguage'])) {
    $options['translationsLanguageCode'] = $_SESSION['installationLanguage'];
}

// Because module init is skipped, we have to initialize translations manually
$translator = \Ip\ServiceLocator::translator();
$translator->setLocale($options['translationsLanguageCode']);

$trPluginDir = ipFile('Plugin/Install/translations/');
$trOverrideDir = ipFile('file/translations/override/');
$translator->addTranslationFilePattern('json', $trPluginDir, 'Install-%s.json', 'Install');
$translator->addTranslationFilePattern('json', $trOverrideDir, 'Install-%s.json', 'Install');

$request = new \Plugin\Install\Request();
$request->setQuery($_GET);
$request->setPost($_POST);
$request->setServer($_SERVER);
$request->setRequest($_REQUEST);

\Ip\ServiceLocator::addRequest($request);

$language = new \Ip\Language(null, $options['translationsLanguageCode'], null, null, null, 0, 'ltr');
ipContent()->_setCurrentLanguage($language);

\Ip\ServiceLocator::dispatcher()->_bindInstallEvents();

if ($request->isGet()) {
    $controller = new \Plugin\Install\PublicController();
    if (!empty($_GET['pa']) && $_GET['pa'] == 'Install.testSessions') {
        $response = $controller->testSessions();
    } else {
        $response = $controller->index();
    }
} elseif ($request->isPost()) {
    $route = Ip\Internal\Core\Job::ipRouteAction_20(array('request' => $request));
    if (!$route || $route['plugin'] != 'Install' || $route['controller'] != 'PublicController') {
        $response = new \Ip\Response\PageNotFound();
    } else {
        $controller = new \Plugin\Install\PublicController();
        $response = $controller->{$route['action']}();
    }
} else {
    exit('HTTP Method not supported.');
}


\Ip\ServiceLocator::removeRequest();

\Ip\ServiceLocator::setResponse($response);
$application->handleResponse($response);

