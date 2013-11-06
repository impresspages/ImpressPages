<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Module\Install;


class Application
{
    public function init()
    {
        define('INSTALL', 'true');

        define('TARGET_VERSION', '3.6');

        //$_SESSION['step'] - stores the value of completed steps

        date_default_timezone_set('Europe/Vilnius'); //PHP 5 requires timezone to be set.

        session_start();
    }

    public function run()
    {
        $this->init();

        if (!isset($_SESSION['step'])) {
            $_SESSION['step'] = 0;
        }

        $cur_step = $_SESSION['step'];

        if (isset($_GET['step'])) {
            $cur_step = $_GET['step'];
        }

        //if ($cur_step > $_SESSION['step']+1) {
        //    $cur_step = $_SESSION['step']+1;
        //}

        // TODOX check if install is done
        //if(!install_available()){
        //    $_SESSION['step'] = 5;
        //    $cur_step = 5;
        //}

        // require('install_'.$cur_step.'.php');

        if (get_magic_quotes_gpc()) {
            \Ip\Internal\Scripts::fixMagicQuotes();
        }

        ini_set('display_errors', 1);

        try {
            \Ip\Core\Application::init();

            $language = 'en';

            // TODOX more intelligent check
            if (isset($_GET['lang']) && file_exists(\ip\Config::coreModuleFile('Install/languages/' . $_GET['lang'] . '.php'))) {
                $_SESSION['installation_language'] = $_GET['lang'];
                $language = $_GET['lang'];
            } elseif (isset($_SESSION['installation_language'])) {
                $language = $_SESSION['installation_language'];
            }

            \Ip\Translator::init($language);
            \Ip\Translator::addTranslationFilePattern('phparray', \ip\Config::coreModuleFile('Install/languages'), '%s.php', 'ipInstall');
            $application = new \Ip\Core\Application();

            $controller = new \Ip\Module\Install\SiteController();

            $action = \Ip\Request::getRequest('a', 'step' . $cur_step);

            // TODOX check if method exists
            $response = $controller->$action();

            $application->handleResponse($response);

        } catch (\Exception $e) {
            throw $e;
        }
    }

}