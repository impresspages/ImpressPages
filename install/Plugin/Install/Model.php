<?php
/**
 * @package   ImpressPages
 */

namespace Plugin\Install;
use \Ip\Db;

class Model
{
    public static function completeStep($step)
    {
        //if($_SESSION['step'] < $step)
        $_SESSION['step'] = $step;
    }

    public static function checkRequirements()
    {
        $error = array();
        $warning = array();

        if (function_exists('apache_get_modules')) {
            if (!in_array('mod_rewrite', apache_get_modules()))
                $error['mod_rewrite'] = 1;
        }

        if (function_exists('apache_get_modules')) {
            if (!in_array('mod_rewrite', apache_get_modules()))
                $error['mod_rewrite'] = 1;
        }

        if (!class_exists('PDO')) {
            $error['mod_pdo'] = 1;
        }

        if (!file_exists(ipFile('.htaccess'))) {
            $error['htaccess'] = 1;
        }

        if (file_exists(ipFile('index.html'))) {
            $error['index.html'] = 1;
        }

        if (!extension_loaded('gd') || !function_exists('gd_info')) {
            $error['gd_lib'] = 1;
        }

        if (get_magic_quotes_gpc()) {
            $warning['magic_quotes'] = 1;
        }

        if (!function_exists('curl_init')) {
            $warning['curl'] = 1;
        }

        if (session_id() == '') { //session hasn't been started
            $warning['session'] = 1;
        }

        $answer = '<h1>' . __('System check', 'Install') . "</h1>";

        $requirements = array();

        $check = array();
        $check['name'] = __('PHP version >= 5.3', 'Install');
        $check['type'] = isset($error['php_version']) ? 'error' : 'success';
        $requirements[] = $check;

        $check = array();
        $check['name'] = __('Apache module "mod_rewrite"', 'Install');
        $check['type'] = isset($error['mod_rewrite']) ? 'error' : 'success';
        $requirements[] = $check;

        $check = array();
        $check['name'] = __('PHP module "PDO"', 'Install');
        $check['type'] = isset($error['mod_pdo']) ? 'error' : 'success';
        $requirements[] = $check;

        $check = array();
        $check['name'] = __('GD Graphics Library', 'Install');
        $check['type'] = isset($error['gd_lib']) ? 'error' : 'success';
        $requirements[] = $check;

        //sessions are checked using curl. If there is no curl, session availability hasn't been checked
        if (!isset($warning['curl'])) {
            $check = array();
            $check['name'] = __('PHP sessions', 'Install');
            $check['type'] = isset($error['session']) ? 'error' : 'success';
            $requirements[] = $check;
        }

        $check = array();
        $check['name'] = __('.htaccess file', 'Install');
        $check['type'] = isset($error['htaccess']) ? 'error' : 'success';
        $requirements[] = $check;

        $check = array();
        $check['name'] = __('index.html removed', 'Install');
        $check['type'] = isset($error['index.html']) ? 'error' : 'success';
        $requirements[] = $check;

        $check = array();
        $check['name'] = __('Magic quotes off (optional)', 'Install');
        $check['type'] = isset($error['magic_quotes']) ? 'error' : 'success';
        $requirements[] = $check;

        $check = array();
        $check['name'] = __('PHP module "Curl"', 'Install');
        $check['type'] = isset($error['curl']) ? 'warning' : 'success';
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
        if (!Helper::isDirectoryWritable(ipFile('file/'))) {
            $check['type'] = 'error';
            $error['writable_file'] = 1;
        } else {
            $check['type'] = 'success';
        }
        $requirements[] = $check;

        $check = array();
        $check['name'] = '<b>/Theme/</b> ' . __('writable', 'Install');
        if (!Helper::isDirectoryWritable(ipFile('Theme'))) {
            $check['type'] = 'error';
            $error['writable_themes'] = 1;
        } else {
            $check['type'] = 'success';
        }
        $requirements[] = $check;

        $check = array();
        $check['name'] = '<b>/config.php</b> ' . __('writable', 'Install');
        if (
            is_file(ipFile('config.php')) && !is_writable(ipFile('config.php'))
            ||
            !is_file(ipFile('config.php')) && !is_writable(ipFile(''))
        ) {
            $check['type'] = 'error';
            $error['writable_config'] = 1;
        } else {
            $check['type'] = 'success';
        }
        $requirements[] = $check;


        $answer .= Helper::generateTable($requirements);

        $answer .= '
        <p class="text-right">';
        if (sizeof($error) > 0) {
            $_SESSION['step'] = 1;
            $answer .= '<a class="btn btn-primary" href="?step=1">' . __('Check again', 'Install') . '</a>';
        } else {
            Model::completeStep(1);
            $answer .= '<a class="btn btn-default" href="?step=1">' . __('Check again', 'Install') . '</a> <a class="btn btn-primary" href="?step=2">' . __('Next', 'Install') . '</a>';
        }
        $answer .= "</p>";

        return $answer;
    }

    public static function createAndUseDatabase($database)
    {
        $db = ipDb();
        try {
            $db->execute('USE `' . $database . '`');
        } catch (\PDOException $e) {
            try {
                $db->execute("CREATE DATABASE `".$database."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
                $db->execute('USE `' . $database . '`');
            } catch (\PDOException $e2) {
                throw new \Ip\Exception('Could not create database');
            }
        }

        return true;
    }

    public static function createDatabaseStructure($database, $tablePrefix)
    {
        $sql = file_get_contents(ipFile('Plugin/Install/sql/structure.sql'));

        $sql = str_replace("[[[[database]]]]", $database, $sql);
        $sql = str_replace("DROP TABLE IF EXISTS `ip_", "DROP TABLE IF EXISTS `". $tablePrefix, $sql);
        $sql = str_replace("CREATE TABLE `ip_", "CREATE TABLE `".$tablePrefix, $sql);

        $errors = array();
        ipDb()->execute($sql);

        return $errors;
    }

    public static function importData($tablePrefix)
    {
        $errors = array();

        $sqlFile = ipFile('Plugin/Install/sql/data.sql');
        $fh = fopen($sqlFile, 'r');
        $sql = fread($fh, utf8_decode(filesize($sqlFile)));
        fclose($fh);


        $sql = str_replace("INSERT INTO `ip_", "INSERT INTO `". $tablePrefix, $sql);
        $sql = str_replace("[[[[version]]]]", ipApplication()->getVersion(), $sql);
        $sql = str_replace("[[[[dbversion]]]]", \Ip\Internal\Update\Model::getDbVersion(), $sql);
        $sql = str_replace("[[[[time]]]]", date('Y-m-d H:i:s'), $sql);

        ipDb()->execute($sql);

        return $errors;
    }

    public static function writeConfigFile($config, $filename)
    {
        $configInfo = array(
            // GLOBAL
            'sessionName' => array(
                'value' => 'changeThis',
                'comment' => 'prevents session conflict when two sites runs on the same server',
            ),
            'developmentEnvironment' => array(
                'value' => 1,
                'comment' => 'displays error and debug information. Change to 0 before deployment to production server',
            ),
            'errorsShow' => array(
                'value' => 1,
                'comment' => "0 if you don't wish to display errors on the page",
            ),
            'debugMode' => array(
                'value' => 0,
                'comment' => "Debug mode loads raw unminified JS files, alerts AJAX errors.",
            ),
            // END GLOBAL


            // FRONTEND
            'timezone' => array(
                'value' => 'changeThis',
                'comment' => 'PHP 5 requires timezone to be set.',
            ),
            // DB
            'db' => array(
                'value' => array(
                    'hostname' => 'localhost',
                    'username' => '',
                    'password' => '',
                    'database' => '',
                    'tablePrefix' => 'ip_',
                    'charset' => 'utf8',
                ),
                'comment' => "Database configuration",
            ),
            // END DB
        );

        // Override template values:
        foreach ($config as $key => $info) {
            $configInfo[$key]['value'] = $config[$key];
        }

        // Generate config code:
        $configCode = "";
        foreach ($configInfo as $key => $info) {
            $exportedString = var_export($info['value'], true);
            if (is_array($info['value'])) {
                $exportedString = self::addSpacesOnNewLines($exportedString);
            }
            $configCode.= "\n    '{$key}' => " . $exportedString . ",";
            if (!empty($info['comment'])) {
                $configCode.= " // " . $info['comment'];
            }
        }

        $configCode = "<"."?php

/**
 * @package ImpressPages
 */

 return array(" . $configCode . "\n);";

        file_put_contents($filename, $configCode);
    }

    protected static function addSpacesOnNewLines($string)
    {
        return preg_replace('/([\r\n]+)/', '$1      ', $string);
    }



    public static function insertAdmin($user, $email, $pass)
    {
        $adminId = \Ip\Internal\Administrators\Service::add($user, $email, $pass);
        \Ip\Internal\AdminPermissionsModel::addPermission('Super admin', $adminId);
    }

    public static function setSiteName($siteName)
    {
        ipSetOption('Config.websiteTitle', $siteName);
    }

    public static function setSiteEmail($siteEmail)
    {
        ipSetOption('Config.websiteEmail', $siteEmail);
    }

    public static function generateCronPassword()
    {
        $password = \rand(100000, 999999);

        ipSetOption('Config.cronPassword', $password);

        return $password;
    }

}
