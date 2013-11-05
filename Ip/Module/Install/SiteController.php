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

    public function step4()
    {
        $dateTimeObject = new \DateTime();
        $currentTimeZone = $dateTimeObject->getTimezone()->getName();
        $timezoneSelectOptions = '';

        $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL_WITH_BC);

        $lastGroup = '';
        foreach($timezones as $timezone) {
            $timezoneParts = explode('/', $timezone);
            $curGroup = $timezoneParts[0];
            if ($curGroup != $lastGroup) {
                if ($lastGroup != '') {
                    $timezoneSelectOptions .= '</optgroup>';
                }
                $timezoneSelectOptions .= '<optgroup label="'.addslashes($curGroup).'">';
                $lastGroup = $curGroup;
            }
            if ($timezone == $currentTimeZone) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            $timezoneSelectOptions .= '<option '.$selected.' value="'.addslashes($timezone).'">'.htmlspecialchars($timezone).'</option>';
        }

        $data = array(
            'timezoneSelectOptions' => $timezoneSelectOptions,
        );

        $content = \Ip\View::create('view/step4.php', $data)->render();

        return $this->applyLayout($content, array('requiredJs' => array('js/step4.js')));
    }

    public function createDatabase()
    {
        $db = \Ip\Request::getPost('db');

        if (strlen($db['tablePrefix']) > strlen('ip_cms_')) {
            return \Ip\Response\JsonRpc::error('ERROR_LONG_PREFIX');
        }

        if (!preg_match('/^([A-Za-z_][A-Za-z0-9_]*)$/', $db['tablePrefix'])) {
            return \Ip\Response\JsonRpc::error('ERROR_INCORRECT_PREFIX');
        }

        // TODOX validate $db
        $dbConfig = array(
            'hostname' => $db['hostname'],
            'username' => $db['username'],
            'password' => $db['password'],
            'tablePrefix' => $db['tablePrefix'],
            'database' => '', // if database doesn't exist, we will create it
            'charset' => 'utf8',
        );

        \Ip\Config::_setRaw('db', $dbConfig);

        try {
            \Ip\Db::getConnection();
        } catch (\Exception $e) {
            return \Ip\Response\JsonRpc::error('ERROR_CONNECT');
        }

        try {
            Model::createAndUseDatabase($db['database']);
        } catch (\Ip\CoreException $e) {
            return \Ip\Response\JsonRpc::error('ERROR_DB');
        }

        $errors = Model::createDatabaseStructure($db['database'], $db['tablePrefix']);

        if (!$errors) {
            $errors = Model::importData($dbConfig['tablePrefix']);
        }

        if ($errors){
            if($_SESSION['step'] < 3)
                $_SESSION['step'] = 3;

            $_SESSION['db_server'] = $_POST['server'];
            $_SESSION['db_db'] = $_POST['db'];
            $_SESSION['db_user'] = $_POST['db_user'];
            $_SESSION['db_pass'] = $_POST['db_pass'];
            $_SESSION['db_prefix'] = $_POST['prefix'];

        }

        if ($errors) {
            return \Ip\Response\JsonRpc::error('ERROR_DB');
        } else {
            Model::completeStep(3);
            return \Ip\Response\JsonRpc::result(true);
        }
    }

    public function writeConfig()
    {
        // Validate input:
        $errors = array();

        if ($_POST['site_name'] == '') {
            $errors[] = 'ERROR_SITE_NAME';
        }

        $emailRegexp = '#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si';

        if ($_POST['site_email'] == '' || !preg_match($emailRegexp, $_POST['site_email'])) {
            $errors[] = 'ERROR_SITE_EMAIL';
        }

        if (!isset($_POST['install_login']) || !isset($_POST['install_pass']) || $_POST['install_login'] == '' || $_POST['install_pass'] == '') {
            $errors[] = 'ERROR_LOGIN';
        }

        if (isset($_POST['timezone']) && $_POST['timezone'] != '') {
            $timezone = $_POST['timezone'];
        } else {
            $errors[] = 'ERROR_TIME_ZONE';
        }

        if ($_POST['email'] != '' && !preg_match($emailRegexp, $_POST['email'])) {
            $errors[] = 'ERROR_EMAIL';
        }

        if (sizeof($errors) > 0) {
            return \Ip\Response\JsonRpc::error(implode(' ', $errors));
        }

        $config = array();
        $config['SESSION_NAME'] = 'ses' . rand();
        $config['BASE_DIR'] = get_parent_dir();
        $config['BASE_URL'] = get_parent_url();
        $config['ERRORS_SEND'] = $_POST['email'];
        $config['timezone'] = $timezone;
        $config['db'] = array(
            'hostname' => $_SESSION['db_server'],
            'username' => $_SESSION['db_user'],
            'password' => $_SESSION['db_pass'],
            'database' => $_SESSION['db_db'],
            'tablePrefix' => $_SESSION['db_prefix'],
            'charset' => 'utf8',
        );

        Model::writeConfig($config, \Ip\Config::baseFile('install/test/ip_config.php'));

        $robots =
            'User-agent: *
            Disallow: /ip_cms/
            Disallow: /ip_configs/
            Disallow: /update/
            Disallow: /install/
            Disallow: /admin.php
            Disallow: /ip_backend_frames.php
            Disallow: /ip_backend_worker.php
            Disallow: /ip_config.php
            Disallow: /ip_cron.php
            Disallow: /ip_license.html
            Disallow: /readme.md
            Sitemap: '.get_parent_url().'sitemap.php';

        $myFile = "../robots.txt";
        $fh = fopen($myFile, 'w') or die('{errorCode:"ERROR_ROBOTS", error:""}');
        fwrite($fh, $robots);
        fclose($fh);

        \Ip\Db::disconnect();

        \Ip\Config::_setRaw('db', $config['db']);

        try {
            \Ip\Db::getConnection();
        } catch (Exception $e) {
            return \Ip\Response\JsonRpc::error('ERROR_CONNECT');
        }

        try {
            $sql = "update `" .\Ip\Db::tablePrefix() . "user` set pass = ?, name = ? limit 1";
            \Ip\Db::execute($sql, array(md5($_POST['install_pass']), $_POST['install_login']));

            $sql = "update `".\Ip\Db::tablePrefix()."par_lang` set `translation` = REPLACE(`translation`, '[[[[site_name]]]]', ?)";
            \Ip\Db::execute($sql, array($_POST['site_name']));

            $sql = "update `".\Ip\Db::tablePrefix() . "par_lang` set `translation` = REPLACE(`translation`, '[[[[site_email]]]]', ?)";
            \Ip\Db::execute($sql, array($_POST['site_email']));

        } catch (Exception $e) {
            return \Ip\Response\JsonRpc::error('ERROR_QUERY')->addErrorData('sql', $sql)->addErrorData('mysqlError', \Ip\Db::getConnection()->errorInfo());
        }

        /*TODOX follow the new structure
         *             $sql = "update `".$_SESSION['db_prefix']."mc_misc_contact_form` set `email_to` = REPLACE(`email_to`, '[[[[site_email]]]]', '".mysql_real_escape_string($_POST['site_email'])."') where 1";
         $rs = mysql_query($sql);
         if(!$rs){
         $errorMessage = preg_replace("/[\n\r]/","",$sql.' '.mysql_error());
         die('{errorCode:"ERROR_QUERY", error:"'.addslashes($errorMessage).'"}');
         }*/

        Model::completeStep(4);
    }

    protected function applyLayout($content, $data = array())
    {
        $data['content'] = $content;
        $layout = \Ip\View::create('view/layout.php', $data);

        return $layout;
    }
}