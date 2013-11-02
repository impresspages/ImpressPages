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

        $error = false;

        try {
            \Ip\Db::getConnection();
        } catch (Exception $e) {
            echo '{errorCode:"ERROR_CONNECT", error:""}';
            exit;
        }

        {
            if (!mysql_select_db($_POST['db'], $conn)){
                //try to create
                $rs = mysql_query("CREATE DATABASE `".$_POST['db']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
                if (!$rs) {
                    echo '{errorCode:"ERROR_DB", error:""}';
                    exit;
                }

                if(!mysql_select_db($_POST['db'], $conn)){
                    $error = true;
                    echo '{errorCode:"ERROR_DB", error:""}';
                    exit;
                }
            }


            mysql_query("SET CHARACTER SET utf8", $conn);
            /*structure*/
            $sqlFile = "sql/structure.sql";
            $fh = fopen($sqlFile, 'r');
            $all_sql = fread($fh, filesize($sqlFile));
            fclose($fh);


            $all_sql = str_replace("[[[[database]]]]", $_POST['db'], $all_sql);
            $all_sql = str_replace("TABLE IF EXISTS `ip_cms_", "TABLE IF EXISTS `".$_POST['prefix'], $all_sql);
            $all_sql = str_replace("TABLE IF NOT EXISTS `ip_cms_", "TABLE IF NOT EXISTS `".$_POST['prefix'], $all_sql);
            $sql_list = explode("-- Table structure", $all_sql);

            $errorMessage = '';


            foreach($sql_list as $key => $sql){
                $rs = mysql_query($sql);
                if(!$rs){
                    $error = true;
                    $errorMessage = preg_replace("/[\n\r]/","",$sql.' '.mysql_error());
                    echo $errorMessage;
                }
            }
            /*end structure*/

            /*data*/
            $sqlFile = "sql/data.sql";
            $fh = fopen($sqlFile, 'r');
            $all_sql = fread($fh, utf8_decode(filesize($sqlFile)));
            fclose($fh);

            //$all_sql = utf8_encode($all_sql);
            $all_sql = str_replace("INSERT INTO `ip_cms_", "INSERT INTO `".$_POST['prefix'], $all_sql);
            $all_sql = str_replace("[[[[base_url]]]]", get_parent_url(), $all_sql);
            $sql_list = explode("-- Dumping data for table--", $all_sql);


            foreach($sql_list as $key => $sql){
                $rs = mysql_query($sql);
                if(!$rs) {
                    $error = true;
                    $errorMessage = preg_replace("/[\n\r]/","",$sql.' '.mysql_error());
                }
            }

            /*end data*/

            define('BASE_DIR', get_parent_dir());
            define('BACKEND', 1);
            define('INCLUDE_DIR', 'ip_cms/includes/');
            define('MODULE_DIR', 'ip_cms/modules/');
            define('LIBRARY_DIR', 'ip_libs/');
            define('DB_PREF', $_POST['prefix']);
            define('THEME', 'Blank');
            define('THEME_DIR', 'ip_themes/');


            require \Ip\Config::includePath('db.php');
            require \Ip\Config::includePath('parameters.php');
            require (__DIR__.'/themeParameters.php');
            require_once(BASE_DIR.'ip_cms/modules/developer/localization/manager.php');

            global $parametersMod;
            $parametersMod = new parametersMod();


            \Modules\developer\localization\Manager::saveParameters(__DIR__.'/parameters.php');

            \Modules\developer\localization\Manager::saveParameters(__DIR__.'/themeParameters.php');

            if($error) {
                echo '{errorCode:"ERROR_QUERY", error:"'.addslashes($errorMessage).'"}';
            }


        }
        mysql_close($conn);
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