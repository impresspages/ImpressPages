<?php
/**
 * @package   ImpressPages
 */

namespace Plugin\Install;


class Application
{
    public function run()
    {
        define('INSTALL', 'true');
        define('TARGET_VERSION', '3.6');

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


            $language = 'en';
            // TODOX more intelligent check
            if (isset($_GET['lang']) && file_exists(ipConfig()->pluginFile('Install/languages/' . $_GET['lang'] . '.php'))) {
                $_SESSION['installation_language'] = $_GET['lang'];
                $language = $_GET['lang'];
            } elseif (isset($_SESSION['installation_language'])) {
                $language = $_SESSION['installation_language'];
            }
            \Ip\Translator::init($language);
            \Ip\Translator::addTranslationFilePattern('phparray', ipConfig()->pluginFile('Install/languages'), '%s.php', 'ipInstall');
            \Ip\Translator::addTranslationFilePattern('phparray', ipConfig()->coreFile('Ip/languages'), 'ipAdmin-%s.php', 'ipAdmin');
            \Ip\Translator::addTranslationFilePattern('phparray', ipConfig()->coreFile('Ip/languages'), 'ipPublic-%s.php', 'ipPublic');

            $controller = new \Plugin\Install\SiteController();

            $action = ipRequest()->getRequest('a', 'step' . $cur_step);

            // TODOX check if method exists
            $response = $controller->$action();
            return $response;
    }

}