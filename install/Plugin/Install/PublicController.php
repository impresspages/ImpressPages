<?php
/**
 * @package   ImpressPages
 */

namespace Plugin\Install;



class PublicController extends \Ip\Controller
{
    protected function init()
    {
        if (ipRequest()->getRequest('debug') !== NULL) {
            $_SESSION['install_debug'] = (int)ipRequest()->getRequest('debug');
        }

        if (!empty($_SESSION['install_debug'])) {
            error_reporting(E_ALL);
            ini_set("display_errors", 1);
        }


        if (empty($_SESSION['websiteId'])) {
            $_SESSION['websiteId'] = Helper::randString(32);
        }


        if (empty($_SESSION['step'])) {
            $_SESSION['step'] = 0;
        }

        if (!empty($_GET['lang']) && strlen($_GET['lang']) == 2 && ctype_alpha($_GET['lang'])) {
            $_SESSION['installation_language'] = $_GET['lang'];
        }
        ipAddJs('Plugin/Install/assets/js/jquery.js');

    }

    public function index ()
    {
        $this->init();
        if (isset($_GET['step'])) {
            $step = (int)$_GET['step'];
        } else {
            $step = 0;
        }

        if ($step > $_SESSION['step']) {
            $step = $_SESSION['step'];
        }

//        breaks on serveriai.lt too dangerous
//        if ($_SESSION['step'] > $step) {
//            $_SESSION['step'] = $step;
//        }

        if (!Helper::isInstallAvailable()) {
            $step = 5;
        }

        $method = 'step' . $step;
        return $this->$method();
    }

    protected function step0()
    {
        if (!Helper::isInstallAvailable()) {
            return;
        }

        $this->init();

        $languages = array();
        $languages['en'] = 'English';
        $languages['cn'] = 'Chinese';
        $languages['cs'] = 'Čeština';
        $languages['nl'] = 'Dutch';
        $languages['de'] = 'Deutsch';
        $languages['fr'] = 'French';
        $languages['ja'] = '日本語';
        $languages['lt'] = 'Lietuvių';
        // $languages['pt'] = 'Portugues'; // something is broken with translations
        $languages['pl'] = 'Polski';
        $languages['ro'] = 'Română';
        $languages['ru'] = 'Русский';
        $languages['tr'] = 'Türk';

        $selected_language = isset($_SESSION['installationLanguage']) ? $_SESSION['installationLanguage'] : 'en';

        $data['selectedLanguage'] = array_key_exists($selected_language, $languages) ? $selected_language : 'en';
        $data['languages'] = $languages;

        $content = ipView('view/step0.php', $data)->render();

        $response = new LayoutResponse();
        $response->setContent($content);

        ipAddJs('Plugin/Install/assets/js/step0.js');

        return $response;
    }

    protected function step1()
    {
        if (!Helper::isInstallAvailable()) {
            return;
        }

        $this->init();

        $checkResults = Model::checkRequirements();
        $errors = $checkResults['errors'];
        $warnings = $checkResults['warnings'];


        $requirements = array();

        $check = array();
        $check['name'] = __('PHP version >= 5.3', 'Install');
        $check['type'] = isset($errors['php_version']) ? 'error' : 'success';
        $requirements[] = $check;

        $check = array();
        $check['name'] = __('PHP module "PDO"', 'Install');
        $check['type'] = isset($errors['mod_pdo']) ? 'error' : 'success';
        $requirements[] = $check;

        $check = array();
        $check['name'] = __('GD Graphics Library', 'Install');
        $check['type'] = isset($errors['gd_lib']) ? 'error' : 'success';
        $requirements[] = $check;

        if (!isset($warnings['curl'])) {
            $check = array();
            $check['name'] = __('PHP sessions', 'Install');
            $check['type'] = isset($errors['session']) ? 'error' : 'success';
            $requirements[] = $check;
        }

        $check = array();
        $check['name'] = __('.htaccess file', 'Install');
        $check['type'] = isset($errors['htaccess']) ? 'error' : 'success';
        $requirements[] = $check;

        $check = array();
        $check['name'] = __('index.html removed', 'Install');
        $check['type'] = isset($errors['index.html']) ? 'error' : 'success';
        $requirements[] = $check;

        $check = array();
        $check['name'] = __('Magic quotes off (optional)', 'Install');
        $check['type'] = isset($errors['magic_quotes']) ? 'error' : 'success';
        $requirements[] = $check;

        $check = array();
        $check['name'] = __('Apache module "mod_rewrite"', 'Install');
        $check['type'] = isset($warnings['mod_rewrite']) ? 'warning' : 'success';
        $requirements[] = $check;

        $check = array();
        $check['name'] = __('PHP module "Curl"', 'Install');
        $check['type'] = isset($errors['curl']) ? 'warning' : 'success';
        $requirements[] = $check;

        $check = array();
        $check['name'] = sprintf( __('PHP memory limit (%s)', 'Install'), ini_get('memory_limit'));


        $check['type'] = \Ip\Internal\System\Helper\SystemInfo::getMemoryLimitAsMb() < 100 ? 'warning' : 'success';

        $requirements[] = $check;

        $check = array();
        $check['name'] = '';
        $check['type'] = '';
        $requirements[] = $check;

        $check = array();
        $check['name'] = '<b>/file/</b> ' . __('writable', 'Install') . ' ' . __('(including subfolders and files)', 'Install');
        if (!Helper::isDirectoryWritable(Model::ipFile('file/'))) {
            $check['type'] = 'error';
            $errors['writable_file'] = 1;
        } else {
            $check['type'] = 'success';
        }
        $requirements[] = $check;

        $check = array();
        $check['name'] = '<b>/Theme/</b> ' . __('writable', 'Install');

        // We cannot use Model::ipFile('Theme/') cause it is overriden
        // and points to install/Theme
        if (!Helper::isDirectoryWritable(Model::ipFile('') . 'Theme')) {
            $check['type'] = 'error';
            $errors['writable_themes'] = 1;
        } else {
            $check['type'] = 'success';
        }
        $requirements[] = $check;

        $check = array();
        $check['name'] = '<b>/config.php</b> ' . __('writable', 'Install');
        if (
            is_file(Model::ipFile('config.php')) && !is_writable(Model::ipFile('config.php'))
            ||
            !is_file(Model::ipFile('config.php')) && !is_writable(Model::ipFile(''))
        ) {
            $check['type'] = 'error';
            $errors['writable_config'] = 1;
        } else {
            $check['type'] = 'success';
        }
        $requirements[] = $check;

        $data = array(
            'requirements' => $requirements,
            'errors' => count($errors) > 0
        );

        $content = ipView('view/step1.php', $data)->render();

        $response = new LayoutResponse();
        $response->setContent($content);

        ipAddJs('Plugin/Install/assets/js/step1.js');

        return $response;
    }

    protected function step2()
    {
        if (!Helper::isInstallAvailable()) {
            return;
        }

        $this->init();

        Model::completeStep(2);

        $content = ipView('view/step2.php');

        $response = new LayoutResponse();
        $response->setContent($content);
        ipAddJs('Plugin/Install/assets/js/step2.js');

        return $response;
    }

    protected function step3()
    {
        if (!Helper::isInstallAvailable()) {
            return;
        }

        $this->init();

        if (!isset($_SESSION['db'])) {
            $_SESSION['db'] = array(
                'hostname' => 'localhost',
                'username' => '',
                'password' => '',
                'database' => '',
                'charset' => 'utf8',
                'tablePrefix' => 'ip_'
            );
        }

        $data = array(
            'db' => $_SESSION['db'],
        );

        $content = ipView('view/step3.php', $data)->render();


        $response = new LayoutResponse();
        $response->setContent($content);

        ipAddJs('Plugin/Install/assets/js/ModuleInstall.js');
        ipAddJs('Plugin/Install/assets/js/step3.js');

        return $response;
    }

    protected function step4()
    {
        if (!Helper::isInstallAvailable()) {
            return;
        }

        $this->init();

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

        $content = ipView('view/step4.php', $data)->render();



        $response = new LayoutResponse();
        ipAddJs('Plugin/Install/assets/js/ModuleInstall.js');
        ipAddJs('Plugin/Install/assets/js/step4.js');

        $response->setContent($content);

        return $response;
    }

    protected function step5()
    {
        $this->init();

        $SESSION['step'] = 5;
        $content = ipView('view/step5.php')->render();

        $response = new LayoutResponse();
        $response->setContent($content);

        return $response;
    }


    public function proceed()
    {
        if (!Helper::isInstallAvailable()) {
            return;
        }

        if (in_array($_SESSION['step'], array(0, 1, 2))) {//TODO check if there are no errors in step1
            $_SESSION['step'] = $_SESSION['step']+1;
            return new \Ip\Response\Json(array('status' => 'ok'));
        }
        return new \Ip\Response\Json(array('status' => 'error'));
    }

    public function createDatabase()
    {
        if (!Helper::isInstallAvailable()) {
            return 'Please remove content from config.php file';
        }

        $db = ipRequest()->getPost('db');

        foreach (array('hostname', 'username', 'database') as $key) {
            if (empty($db[$key])) {
                return \Ip\Response\JsonRpc::error(__('Please fill in required fields.', 'Install', false));
            }
        }

        if (empty($db['tablePrefix'])) {
            $db['tablePrefix'] = '';
        }

        if (strlen($db['tablePrefix']) > 7) {
            return \Ip\Response\JsonRpc::error(__('Prefix can\'t be longer than 7 symbols.', 'Install', false));
        }

        if ($db['tablePrefix'] != '' && !preg_match('/^([A-Za-z_][A-Za-z0-9_]*)$/', $db['tablePrefix'])) {
            return \Ip\Response\JsonRpc::error(__('Prefix can\'t contain any special characters and should start with a letter.', 'Install', false));
        }


        $dbConfig = array(
            'hostname' => $db['hostname'],
            'username' => $db['username'],
            'password' => $db['password'],
            'tablePrefix' => $db['tablePrefix'],
            'database' => '', // if database doesn't exist, we will create it
            'charset' => 'utf8',
        );

        ipConfig()->set('db', $dbConfig);

        try {
            ipDb()->getConnection();
        } catch (\Exception $e) {
            return \Ip\Response\JsonRpc::error(__('Can\'t connect to database.', 'Install'), false);
        }

        try {
            Model::createAndUseDatabase($db['database']);
        } catch (\Ip\Exception $e) {
            return \Ip\Response\JsonRpc::error(__('Specified database does not exists and cannot be created.', 'Install', false));
        }


        $tables = array (
            'page',
            'page_storage',
            'permission',
            'language',
            'log',
            'email_queue',
            'repository_file',
            'repository_reflection',
            'widget',
            'theme_storage',
            'widget_order',
            'inline_value_global',
            'inline_value_language',
            'inline_value_page',
            'plugin',
            'storage',
            'administrator'
        );


        $tableExists = FALSE;
        foreach ($tables as $table) {
            try {
                $sql = 'SELECT 1 FROM `' . $dbConfig['tablePrefix'] . $table . '`';
                ipDb()->execute($sql);
                $tableExists = TRUE;
            } catch (\Exception $e) {
                //Do nothing. We have expected this error to occur. That means the database is clean
            }
        }
        if ($tableExists && empty($db['replaceTables'])) {
            return \Ip\Response\JsonRpc::error(__('Do you like to replace existing tables in the database?', 'Install', false), 'table_exist');
        }


        $errors = Model::createDatabaseStructure($db['database'], $db['tablePrefix']);

        if (!$errors) {
            $errors = Model::importData($dbConfig['tablePrefix']);
        }

        if ($errors){
            if($_SESSION['step'] < 3) {
                $_SESSION['step'] = 3;
            }
        }

        $dbConfig['database'] = $db['database'];

        $_SESSION['db'] = $dbConfig;

        if ($errors) {
            return \Ip\Response\JsonRpc::error(__('There were errors while executing install queries. ' . serialize($errors), 'Install', false));
        } else {
            \Ip\ServiceLocator::config()->set('db', $dbConfig);
            OptionHelper::import(__DIR__ . '/options.json');

            Model::completeStep(4);
            return \Ip\Response\JsonRpc::result(true);
        }




    }

    public function writeConfig()
    {
        if (!Helper::isInstallAvailable()) {
            return 'Please remove content from config.php file';
        }


        $this->init();

        if (empty($_SESSION['db'])) {
            return \Ip\Response\JsonRpc::error(__('Session has expired. Please restart your install.', 'Install', 'false'));
        }

        // Validate input:
        $errors = array();

        if (!ipRequest()->getPost('siteName')) {
            $errors[] = __('Please enter website name.', 'Install', false);
        }

        if (!ipRequest()->getPost('siteEmail') || !filter_var(ipRequest()->getPost('siteEmail'), FILTER_VALIDATE_EMAIL)) {
            $errors[] = __('Please enter correct website email.', 'Install', false);
        }

        if (!ipRequest()->getPost('install_login') || !ipRequest()->getPost('install_pass')) {
            $errors[] = __('Please enter administrator login and password.', 'Install', false);
        }

        if (ipRequest()->getPost('timezone')) {
            $timezone = ipRequest()->getPost('timezone');
        } else {
            $errors[] = __('Please choose website time zone.', 'Install', false);
        }


        if (!empty($errors)) {
            return \Ip\Response\JsonRpc::error(__('Please correct errors.', 'Install', false))->addErrorData('errors', $errors);
        }

        $config = array();
        $config['sessionName'] = 'ses' . rand();
        $config['timezone'] = $timezone;
        $config['db'] = $_SESSION['db'];

        if (empty($_SESSION['rewritesEnabled'])) {
            $config['rewritesDisabled'] = true;
        }

        try {
            Model::writeConfigFile($config, ipFile('config.php'));
        } catch (\Exception $e) {
            return \Ip\Response\JsonRpc::error(__('Can\'t write configuration "/config.php"', 'Install', false));
        }



        try {
            ipConfig()->set('db', $config['db']);
            ipDb()->getConnection();
        } catch (\Exception $e) {
            return \Ip\Response\JsonRpc::error(__('Can\'t connect to database.', 'Install', false));
        }
        try {

            Model::insertAdmin(ipRequest()->getPost('install_login'), ipRequest()->getPost('siteEmail'), ipRequest()->getPost('install_pass'));
            ipSetOptionLang('Config.websiteTitle', ipRequest()->getPost('siteName'), 'en');
            ipSetOptionLang('Config.websiteEmail', ipRequest()->getPost('siteEmail'), 'en');
            Model::generateCronPassword();
            ipStorage()->set('Ip', 'cachedBaseUrl', substr(ipConfig()->baseUrl(), 0, - strlen('install')));
            ipStorage()->set('Ip', 'websiteId', $_SESSION['websiteId']);
        } catch (\Exception $e) {
            return \Ip\Response\JsonRpc::error($e->getTraceAsString());
        }

        Model::completeStep(5);

        return \Ip\Response\JsonRpc::result(true);
    }


    protected function getParentUrl() {
        $pageURL = '';
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }

        $pageURL = substr($pageURL, 0, strrpos($pageURL, '/'));
        $pageURL = substr($pageURL, 0, strrpos($pageURL, '/') + 1);
        return $pageURL;
    }


}
