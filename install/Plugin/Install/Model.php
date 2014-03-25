<?php
/**
 * @package   ImpressPages
 */

namespace Plugin\Install;


class Model
{
    protected static $installationDir = null;

    public static function setInstallationDir($installationDir)
    {
        self::$installationDir = $installationDir;
    }

    public static function ipFile($path)
    {
        $path = str_replace('Plugin/', 'install/Plugin/', $path);
        if (self::$installationDir) {
            return self::$installationDir . $path;
        } else {
            return ipFile($path);
        }
    }

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

        if (!file_exists(self::ipFile('.htaccess'))) {
            $error['htaccess'] = 1;
        }

        if (file_exists(self::ipFile('index.html'))) {
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
        $answer = array(
            'errors' => $error,
            'warnings' => $warning
        );
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
        $sql = file_get_contents(self::ipFile('Plugin/Install/sql/structure.sql'));

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

        $sqlFile = self::ipFile('Plugin/Install/sql/data.sql');
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
            'showErrors' => array(
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
