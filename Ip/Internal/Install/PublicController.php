<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Install;

use Ip\Response;
use Ip\Response\Redirect;

class PublicController extends \Ip\Controller
{
    protected function init()
    {
        if (ipRequest()->getRequest('debug') !== NULL) {
            $_SESSION['install_debug'] = (int)ipRequest()->getRequest('debug');
        }

        if (!empty($_SESSION['install_debug'])) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }

        if (empty($_SESSION['websiteId'])) {
            $_SESSION['websiteId'] = Helper::randString(32);
        }
    }

    public function testSessions()
    {
        if (!empty($_SESSION['websiteId'])) {
            return new \Ip\Response\Json(array('status' => 'success'));
        }

        $answer = array(
            'html' => ipView('view/sessionsDontWork.php')->render()
        );
        return new \Ip\Response\Json($answer);

    }

    public function index()
    {
        $this->init();

        if (isset($_GET['step']) && $_GET['step'] == 'check-rewrites') {
            $_SESSION['rewritesEnabled'] = true;
            return new Response('OK');
        }

        if (isset($_GET['step'])) {
            $step = (int)$_GET['step'];
        } else {
            $step = Helper::$firstStep;
        }

        if ($step < Helper::$firstStep) {
            $step = Helper::$firstStep;
        }

        // going to the last step
        if (!Helper::isInstallAvailable() || $step > Helper::$lastStep) {
            return new Redirect(ipConfig()->baseUrl());
        }

        switch ($step) {
            case '1':
                $response = $this->configuration();
                break;
            case '2':
                $response = $this->systemCheck();
                break;
            case '3':
                $response = $this->database();
                break;
            case '4':
                $response = $this->finish();
                break;
            default:
                $response = new LayoutResponse();
        }

        $response->setLayout(ipFile('Ip/Internal/Install/view/layout.php'));
        return $response;
    }

    protected function configuration()
    {
        $timezoneSelectOptions = Helper::getTimezoneSelectOptions();

        if (!isset($_SESSION['config'])) {
            $_SESSION['config'] = array(
                'websiteName' => '',
                'websiteEmail' => '',
                'timezone' => '',
                'support' => 1
            );
        }

        $data = array(
            'config' => $_SESSION['config'],
            'timezoneSelectOptions' => $timezoneSelectOptions,
        );

        return Helper::renderLayout('view/configuration.php', $data);
    }

    protected function systemCheck()
    {

        $requirements = [];

        if (!Helper::isApache() && !Helper::isNginx()) {
            $requirements[] = array(
                'name' => __('Apache or Nginx required', 'Install'),
                'type' => 'warning'
            );
        }
        if (Helper::isNginx()) {
            $requirements[] = array(
                'name' => __('Nginx configuration required', 'Install'),
                'helpUrl' => 'http://www.impresspages.org/help/nginx',
                'type' => 'warning'
            );
        }
        $requirements[] = array(
            'name' => __('PHP version >= 5.5', 'Install'),
            'helpUrl' => 'http://www.impresspages.org/help/php55',
            'type' => Helper::checkPhpVersion()
        );
        $requirements[] = array(
            'name' => __('PHP module "PDO"', 'Install'),
            'helpUrl' => 'http://www.impresspages.org/help/pdo',
            'type' => Helper::checkPDO()
        );
        $requirements[] = array(
            'name' => __('PHP module "XML"', 'Install'),
            'helpUrl' => 'http://www.impresspages.org/help/xml',
            'type' => Helper::checkLibXml()
        );
        $requirements[] = array(
            'name' => __('GD Graphics Library', 'Install'),
            'helpUrl' => 'http://www.impresspages.org/help/gd',
            'type' => Helper::checkGD()
        );
        $requirements[] = array(
            'name' => __('PHP sessions', 'Install'),
            'helpUrl' => 'http://www.impresspages.org/help/sessions',
            'type' => Helper::checkPhpSessions()
        );
        $requirements[] = array(
            'name' => __('index.html removed', 'Install'),
            'helpUrl' => 'http://www.impresspages.org/help/index-html',
            'type' => Helper::checkFileIndexDotHtml()
        );
        if (Helper::isApache()) {
            $requirements[] = array(
                'name' => __('Apache module "mod_rewrite"', 'Install'),
                'helpUrl' => 'http://www.impresspages.org/help/mod-rewrite',
                'type' => Helper::checkModRewrite()
            );
            $requirements[] = array(
                'name' => __('.htaccess file', 'Install'),
                'helpUrl' => 'http://www.impresspages.org/help/htaccess',
                'type' => Helper::checkFileDotHtaccess()
            );
        }
        $requirements[] = array(
            'name' => __('PHP module "Curl"', 'Install'),
            'helpUrl' => 'http://www.impresspages.org/help/curl',
            'type' => Helper::checkCurl()
        );
        $requirements[] = array(
            'name' => sprintf( __('PHP memory limit (%s)', 'Install'), ini_get('memory_limit')),
            'helpUrl' => 'http://www.impresspages.org/help/php-memory',
            'type' => Helper::checkMemoryLimit()
        );

        if (!ipConfig()->isComposerCore()) {
            $requirements[] = array(
                'name' => '/Ip/ ' . __('writable', 'Install') . ' ' . __('(including subfolders and files)', 'Install'),
                'helpUrl' => 'http://www.impresspages.org/help/ip-dir-writable',
                'type' => Helper::checkFolderIp()
            );
        }

        $requirements[] = array(
            'name' => '/Plugin/ ' . __('writable (including subfolders and files)', 'Install'),
            'helpUrl' => 'http://www.impresspages.org/help/plugin-dir-writable',
            'type' => Helper::checkFolderPlugin()
        );
        $requirements[] = array(
            'name' => '/Theme/ ' . __('writable (including subfolders and files)', 'Install'),
            'helpUrl' => 'http://www.impresspages.org/help/theme-dir-writable',
            'type' => Helper::checkFolderTheme()
        );
        $requirements[] = array(
            'name' => '/file/ ' . __('writable', 'Install') . ' ' . __('(including subfolders and files)', 'Install'),
            'helpUrl' => 'http://www.impresspages.org/help/file-dir-writable',
            'type' => Helper::checkFolderFile()
        );
        $requirements[] = array(
            'name' => '/config.php ' . __('writable', 'Install'),
            'helpUrl' => 'http://www.impresspages.org/help/config-writable',
            'type' => Helper::checkFileConfigPhp()
        );

        $showNextStep = true;
        $autoForward = true;
        $notSuccess = [];
        foreach ($requirements as $req) {
            if ($req['type'] == 'success') { continue; } // skipping

            // Force to repeat system check
            if ($req['type'] == 'error') {
                $showNextStep = false;
            }

            // If something isn't perfect we collect and show to user
            $autoForward = false;
            $notSuccess[] = $req;
        }

        $data = array(
            'requirements' => $requirements,
            'showNextStep' => $showNextStep
        );

        // Send usage statistics
        $usageStatistics = Helper::setUsageStatistics('Install.systemCheck', $notSuccess);
        \Ip\Internal\System\Model::sendUsageStatistics($usageStatistics);

        if ($autoForward) {
            header('Location: ' . ipConfig()->baseUrl() . 'index.php?step=3') ;
            exit;
        }

        return Helper::renderLayout('view/system.php', $data);
    }

    protected function database()
    {
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

        return Helper::renderLayout('view/database.php', $data);
    }

    protected function finish()
    {
        // cleaning session data (logins, passwords, etc.)
        if (isset($_SESSION['config'])) { $_SESSION['config'] = null; }
        if (isset($_SESSION['db'])) { $_SESSION['db'] = null; }
        if (isset($_SESSION['db_errors'])) { $_SESSION['db_errors'] = null; }

        $showInfo = false;
        // Showing extra info if user tries to get back when installation is finished
        if (isset($_GET['step']) && (int)$_GET['step'] < Helper::$lastStep) {
            $showInfo = true;
        }

        $data = array(
            'showInfo' => $showInfo,
        );

        return Helper::renderLayout('view/finish.php', $data);
    }

    public function testConfiguration()
    {
        if (!Helper::isInstallAvailable()) {
            return sprintf(__('Please remove content from %s file.', 'Install', false), 'config.php');
        }

        // Validating input
        $errors = [];

        // Website name
        if (!Helper::validateWebsiteName(ipRequest()->getPost('configWebsiteName'))) {
            $errors[] = __('Please enter website name.', 'Install', false);
        }

        // Website email
        if (!Helper::validateWebsiteEmail(ipRequest()->getPost('configWebsiteEmail'))) {
            $errors[] = __('Please enter correct website email.', 'Install', false);
        }

        // Website timezone
        if (!Helper::validateTimezone(ipRequest()->getPost('configTimezone'))) {
            $errors[] = __('Please choose website time zone.', 'Install', false);
        }

        // Support
        // ipRequest()->getPost('configSupport')

        // Let's save config data to user session
        if (ipRequest()->getPost('configWebsiteName')) {
            $_SESSION['config']['websiteName'] = ipRequest()->getPost('configWebsiteName');
        }
        if (ipRequest()->getPost('configWebsiteEmail')) {
            $_SESSION['config']['websiteEmail'] = ipRequest()->getPost('configWebsiteEmail');
        }
        if (ipRequest()->getPost('configTimezone')) {
            $_SESSION['config']['timezone'] = ipRequest()->getPost('configTimezone');
        }
        if (ipRequest()->getPost('configSupport') !== null) {
            $_SESSION['config']['support'] = ipRequest()->getPost('configSupport');
        }

        // Send usage statistics
        $usageStatistics = Helper::setUsageStatistics('Install.configuration', $errors);
        \Ip\Internal\System\Model::sendUsageStatistics($usageStatistics);

        if (!empty($errors)) {
            return \Ip\Response\JsonRpc::error(__('Please correct errors.', 'Install', false))->addErrorData('errors', $errors);
        }

        return \Ip\Response\JsonRpc::result(array('redirect' => 'index.php?step=2'));
    }

    public function createDatabase()
    {
        if (!Helper::isInstallAvailable()) {
            return sprintf(__('Please remove content from %s file.', 'Install', false), 'config.php');
        }

        $db = ipRequest()->getPost('db');
        if (!isset($_SESSION['db_errors'])) {
            $_SESSION['db_errors'] = [];
        }

        foreach (array('hostname', 'username', 'database') as $key) {
            if (empty($db[$key])) {
                $_SESSION['db_errors'][] = 'Required fields';
                return \Ip\Response\JsonRpc::error(__('Please fill in required fields.', 'Install', false));
            }
        }

        if (empty($db['tablePrefix'])) {
            $db['tablePrefix'] = '';
        }

        if (strlen($db['tablePrefix']) > 7) {
            $_SESSION['db_errors'][] = 'Prefix too long';
            return \Ip\Response\JsonRpc::error(__("Prefix can't be longer than 7 symbols.", 'Install', false));
        }

        if ($db['tablePrefix'] != '' && !preg_match('/^([A-Za-z_][A-Za-z0-9_]*)$/', $db['tablePrefix'])) {
            $_SESSION['db_errors'][] = 'Prefix is bad';
            return \Ip\Response\JsonRpc::error(__("Prefix can't contain any special characters and should start with a letter.", 'Install', false));
        }

        $dbConfig = array(
            'hostname' => $db['hostname'],
            'username' => $db['username'],
            'password' => $db['password'],
            'tablePrefix' => $db['tablePrefix'],
            'database' => '', // If database doesn't exist, we will create it.
            'charset' => 'utf8',
        );

        ipConfig()->set('db', $dbConfig);

        try {
            ipDb()->getConnection();
        } catch (\Exception $e) {
            $_SESSION['db_errors'][] = 'Cannot connect';
            return \Ip\Response\JsonRpc::error(__("Can't connect to database.", 'Install'), false);
        }

        try {
            Model::createAndUseDatabase($db['database']);
        } catch (\Ip\Exception $e) {
            $_SESSION['db_errors'][] = 'DB cannot be created';
            return \Ip\Response\JsonRpc::error(__('Specified database does not exists and cannot be created.', 'Install', false));
        }

        if (Helper::testDBTables($db['tablePrefix']) && empty($db['replaceTables'])) {
            $_SESSION['db_errors'][] = 'Replace tables';
            return \Ip\Response\JsonRpc::error(__('Do you like to replace existing tables in the database?', 'Install', false), 'table_exists');
        }

        $errors = Model::createDatabaseStructure($db['database'], $db['tablePrefix']);

        if (!$errors) {
            $errors = Model::importData($dbConfig['tablePrefix']);
        }

        if ($errors) {
            $_SESSION['db_errors'][] = 'Failed install';
            return \Ip\Response\JsonRpc::error(__('There were errors while executing install queries. ' . serialize($errors), 'Install', false));
        }

        $dbConfig['database'] = $db['database'];
        $_SESSION['db'] = $dbConfig;

        $configToFile = [];
        $configToFile['sessionName'] = 'ses' . rand();
        $configToFile['db'] = $_SESSION['db'];
        $configToFile['timezone'] = $_SESSION['config']['timezone'];

        if (Helper::checkModRewrite() != 'success') {
            $configToFile['rewritesDisabled'] = true;
        }

        $admin = ipRequest()->getPost('admin');
        if ($admin) {
            $adminUsername = $admin['username'];
            $adminEmail = $admin['email'];
            $adminPassword = $admin['password'];
        }

        $cachedBaseUrl = ipConfig()->baseUrl();

        try {
            ipConfig()->set('db', $dbConfig);

            // if admin data is posted then user will be created
            if ($admin) {
                Model::insertAdmin($adminUsername, $adminEmail, $adminPassword);
            }
            ipSetOptionLang('Config.websiteTitle', $_SESSION['config']['websiteName'], 'en');
            ipSetOptionLang('Config.websiteEmail', $_SESSION['config']['websiteEmail'], 'en');
            Model::generateCronPassword();
            ipStorage()->set('Ip', 'cachedBaseUrl', $cachedBaseUrl);
            ipStorage()->set('Ip', 'websiteId', $_SESSION['websiteId']);
            ipStorage()->set('Ip', 'getImpressPagesSupport', $_SESSION['config']['support']);
        } catch (\Exception $e) {
            $_SESSION['db_errors'][] = $e->getTraceAsString();
            return \Ip\Response\JsonRpc::error($e->getTraceAsString());
        }

        try {
                Model::writeConfigFile($configToFile, ipConfig()->configFile());
        } catch (\Exception $e) {
            $_SESSION['db_errors'][] = 'Cannot write config file';
            return \Ip\Response\JsonRpc::error(__('Can\'t write configuration "/config.php"', 'Install', false));
        }

        // Send usage statistics
        $usageStatistics = Helper::setUsageStatistics('Install.database', $_SESSION['db_errors']);
        \Ip\Internal\System\Model::sendUsageStatistics($usageStatistics);

        $redirect = $cachedBaseUrl . 'admin';

        return \Ip\Response\JsonRpc::result(array('redirect' => $redirect));
    }
}
