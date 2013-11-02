<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Module\Install;

class SiteController extends \Ip\Controller
{
    public function step0()
    {
        $selected_language = (isset($_SESSION['installation_language']) ? $_SESSION['installation_language'] : 'en');

        $languages = array();
        $languages['cs'] = 'Čeština';
        $languages['nl'] = 'Dutch';
        $languages['en'] = 'English';
        $languages['fr'] = 'French';
        $languages['de'] = 'Deutsch';
        $languages['ja'] = '日本語';
        $languages['lt'] = 'Lietuvių';
        $languages['pt'] = 'Portugues';
        $languages['pl'] = 'Polski';
        $languages['ro'] = 'Română';

        $data['selectedLanguage'] = $selected_language;
        $data['languages'] = $languages;

        $content = \Ip\View::create('view/step0.php', $data)->render();

        return $this->applyLayout($content);
    }

    public function step1()
    {
        Model::completeStep(1);

        $content = Model::checkRequirements();


        function get_url()
        {
            $pageURL = 'http';
            if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
                $pageURL .= "s";
            }
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            }

            return $pageURL;
        }

        return $this->applyLayout($content);
    }

    public function step2()
    {
        // TODOX Algimantas: what this is for?
        $license = file_get_contents(\Ip\Config::baseFile('ip_license.html'));

        Model::completeStep(2);

        $content = \Ip\View::create('view/step2.php');

        return $this->applyLayout($content);
    }

    public function step3()
    {
        if (!isset($_SESSION['db_server'])) {
            $_SESSION['db_server'] = 'localhost';
        }

        if (!isset($_SESSION['db_user'])) {
            $_SESSION['db_user'] = '';
        }

        if (!isset($_SESSION['db_pass'])) {
            $_SESSION['db_pass'] = '';
        }

        if (!isset($_SESSION['db_db'])) {
            $_SESSION['db_db'] = '';
        }

        if (!isset($_SESSION['db_prefix'])) {
            $_SESSION['db_prefix'] = 'ip_';
        }

        $content = \Ip\View::create('view/step3.php')->render();

        return $this->applyLayout($content, array('requiredJs' => array('js/step3.js')));
    }

    public function createDatabase()
    {
        $db = \Ip\Request::getPost('db');

        if (strlen($db['tablePrefix']) > strlen('ip_cms_')) {
            echo '{errorCode:"ERROR_LONG_PREFIX", error:""}';
            exit;
        }

        if (!preg_match('/^([A-Za-z_][A-Za-z0-9_]*)$/', $db['tablePrefix'])) {
            echo '{errorCode:"ERROR_INCORRECT_PREFIX", error:""}';
            exit;
        }

        // TODOX validate $db
        $dbConfig = array(
            'hostname' => $db['hostname'],
            'username' => $db['username'],
            'password' => $db['password'],
            'tablePrefix' => $db['tablePrefix'],
            'database' => '', // database doesn't exist, we will create it
            'charset' => 'utf8',
        );

        \Ip\Config::_setRaw('db', $dbConfig);

        try {
            \Ip\Db::getConnection();
        } catch (\Exception $e) {
            // TODOX JSON
            return '{errorCode:"ERROR_CONNECT", error:""}';
        }

        try {
            Model:createAndUseDatabase($dbConfig['database']);
        } catch (\Ip\CoreException $e) {
            // TODOX Json
            return '{errorCode:"ERROR_DB", error:""}';
        }

        Model::installDatabase($db);

        if($error == false){
            if($_SESSION['step'] < 3)
                $_SESSION['step'] = 3;

            $_SESSION['db_server'] = $_POST['server'];
            $_SESSION['db_db'] = $_POST['db'];
            $_SESSION['db_user'] = $_POST['db_user'];
            $_SESSION['db_pass'] = $_POST['db_pass'];
            $_SESSION['db_prefix'] = $_POST['prefix'];

        }
    }

    protected function applyLayout($content, $data = array())
    {
        $data['content'] = $content;
        $layout = \Ip\View::create('view/layout.php', $data);

        return $layout->render();
    }
}