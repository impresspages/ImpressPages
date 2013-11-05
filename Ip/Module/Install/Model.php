<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Module\Install;
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

        if (!file_exists(\Ip\Config::baseFile('.htaccess'))) {
            $error['htaccess'] = 1;
        }

        if (file_exists(\Ip\Config::baseFile('index.html'))) {
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

        // TODOX Algimantas: what's that?
        if (function_exists('curl_init')) {
            if (session_id() == '') { //session hasn't been started
                $warning['session'] = 1;
            }
        }

        $answer = '<h1>' . __('SYSTEM_CHECK_LONG', 'ipInstall') . "</h1>";

        $table = array();

        $table[] = __('PHP version >= 5.3', 'ipInstall');
        if (isset($error['php_version']))
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
        else
            $table[] = '<span class="correct">' . __('Yes', 'ipInstall') . '</span>';

        $table[] = __('Apache module "mod_rewrite"', 'ipInstall');
        if (isset($error['mod_rewrite']))
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
        else
            $table[] = '<span class="correct">' . __('Yes', 'ipInstall') . '</span>';


        $table[] = __('PHP module "PDO"', 'ipInstall');
        if (isset($error['mod_pdo']))
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
        else
            $table[] = '<span class="correct">' . __('Yes', 'ipInstall') . '</span>';

        $table[] = __('GD Graphics Library', 'ipInstall');
        if (isset($error['gd_lib']))
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
        else
            $table[] = '<span class="correct">' . __('Yes', 'ipInstall') . '</span>';

//sessions are checked using curl. If there is no curl, session availability hasn't been checked
        if (!isset($warning['curl'])) {
            $table[] = __('PHP sessions', 'ipInstall');
            if (isset($warning['session'])) {
                $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
            } else {
                $table[] = '<span class="correct">' . __('Yes', 'ipInstall') . '</span>';
            }
        }

        $table[] = __('.htaccess file', 'ipInstall');
        if (isset($error['htaccess']))
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
        else
            $table[] = '<span class="correct">' . __('Yes', 'ipInstall') . '</span>';


        $table[] = __('index.html removed', 'ipInstall');
        if (isset($error['index.html']))
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
        else
            $table[] = '<span class="correct">' . __('Yes', 'ipInstall') . '</span>';


        $table[] = __('Magic quotes off (optional)', 'ipInstall');
        if (isset($warning['magic_quotes']))
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
        else
            $table[] = '<span class="correct">' . __('Yes', 'ipInstall') . '</span>';

        $table[] = __('PHP module "Curl"', 'ipInstall');
        if (isset($warning['curl'])) {
            $table[] = '<span class="warning">' . __('Warning', 'ipInstall') . "</span>";
        } else {
            $table[] = '<span class="correct">' . __('Yes', 'ipInstall') . '</span>';
        }

        $table[] = sprintf( __('PHP memory limit (%s)', 'ipInstall'), ini_get('memory_limit'));
        if ((integer)ini_get('memory_limit') < 100) {
            $table[] = '<span class="warning">' . __('Warning', 'ipInstall') . "</span>";
        } else {
            $table[] = '<span class="correct">' . __('Yes', 'ipInstall') . "</span>";
        }


        $table[] = '';
        $table[] = '';


        $table[] = '';
        $table[] = '';


        $table[] = '<b>/file/</b> ' . __('writable', 'ipInstall') . ' ' . __('(including subfolders and files)', 'ipInstall');

        if (!Helper::isDirectoryWritable(\Ip\Config::fileDirFile(''))) {
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
            $error['writable_file'] = 1;
        } else
            $table[] = '<span class="correct">' . __('Yes', 'ipInstall') . '</span>';


        $table[] = '<b>/ip_themes/</b> ' . __('writable', 'ipInstall');
        if (!Helper::isDirectoryWritable(dirname(\Ip\Config::themeFile('')))) {
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
            $error['writable_themes'] = 1;
        } else
            $table[] = '<span class="correct">' . __('Yes', 'ipInstall') . '</span>';


        $table[] = '<b>/ip_config.php</b> ' . __('writable', 'ipInstall');

        if (!is_writable(\Ip\Config::baseFile('ip_config.php'))) {
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
            $error['writable_config'] = 1;
        } else
            $table[] = '<span class="correct">' . __('Yes', 'ipInstall') . '</span>';


        $table[] = '<b>/robots.txt</b> ' . __('writable', 'ipInstall');
        if (!is_writable(\Ip\Config::baseFile('robots.txt'))) {
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
            $error['writable_robots'] = 1;
        } else
            $table[] = '<span class="correct">' . __('Yes', 'ipInstall') . '</span>';


        $answer .= Helper::gen_table($table);

        $answer .= '<br><br>';
        if (sizeof($error) > 0) {
            $_SESSION['step'] = 1;
            $answer .= '<a class="button_act" href="?step=1">' . __('Check again', 'ipInstall') . '</a>';
        } else {
            Model::completeStep(1);
            $answer .= '<a class="button_act" href="?step=2">' . __('Next', 'ipInstall') . '</a><a class="button" href="?step=1">' . __('Check again', 'ipInstall') . '</a>';
        }
        $answer .= "<br>";

        return $answer;
    }

    public static function createAndUseDatabase($database)
    {
        try {
            Db::execute('USE `' . $database . '`');
            /*
            var_export($database);
            echo "\n" . __FILE__ . ":" . __LINE__;
            exit;
            //*/
        } catch (\PDOException $e) {
            /*
            var_export($database);
            echo "\n" . __FILE__ . ":" . __LINE__;
            exit;
            //*/

            try {
                /*
                var_export($database);
                echo "\n" . __FILE__ . ":" . __LINE__;
                exit;
                //*/
                Db::execute("CREATE DATABASE `".$database."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
                Db::execute('USE `' . $database . '`');
            } catch (\PDOException $e2) {
                throw new \Ip\CoreException('Could not create database');
            }
        }

        /*
        var_export($database);
        echo "\n" . __FILE__ . ":" . __LINE__;
        exit;
        //*/

        return true;
    }

    public static function createDatabaseStructure($database, $tablePrefix)
    {
        $all_sql = file_get_contents(\Ip\Config::baseFile('install/sql/structure.sql'));

        $all_sql = str_replace("[[[[database]]]]", $database, $all_sql);
        $all_sql = str_replace("TABLE IF EXISTS `ip_cms_", "TABLE IF EXISTS `". $tablePrefix, $all_sql);
        $all_sql = str_replace("TABLE IF NOT EXISTS `ip_cms_", "TABLE IF NOT EXISTS `".$tablePrefix, $all_sql);
        $sql_list = explode("-- Table structure", $all_sql);

        $errors = array();

        foreach ($sql_list as $sql) {
            try {
                Db::execute($sql);
            } catch (Exception $e) {
                $errors[] = preg_replace("/[\n\r]/", "", $sql . ' ' . Db::getConnection()->errorInfo());
            }
        }

        return $errors;
    }

    public static function importData($tablePrefix)
    {
        $errors = array();

        // TODOX Algimantas: why so complicated?
        $sqlFile = \Ip\Config::baseFile("install/sql/data.sql");
        $fh = fopen($sqlFile, 'r');
        $all_sql = fread($fh, utf8_decode(filesize($sqlFile)));
        fclose($fh);

        //$all_sql = utf8_encode($all_sql);
        $all_sql = str_replace("INSERT INTO `ip_cms_", "INSERT INTO `". $tablePrefix, $all_sql);
        $all_sql = str_replace("[[[[base_url]]]]", \Ip\Config::baseUrl(''), $all_sql);
        $sql_list = explode("-- Dumping data for table--", $all_sql);


        foreach ($sql_list as $sql) {
            try {
                Db::execute($sql);
            } catch (Exception $e) {
                $errors[] = preg_replace("/[\n\r]/", "", $sql . ' ' . Db::getConnection()->errorInfo());
            }
        }

        return $errors;
    }

    public static function writeConfigFile($config, $filename)
    {
        $configInfo = array(
            // GLOBAL
            'SESSION_NAME' => array(
                'value' => 'changeThis',
                'comment' => 'prevents session conflict when two sites runs on the same server',
            ),
            'BASE_DIR' => array(
                'value' => '',
                'comment' => 'root DIR with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.',
            ),
            'CORE_DIR' => array(
                'value' => '',
                'comment' => 'Directory where Ip directory resides',
            ),
            'BASE_URL' => array(
                'value' => '',
                'comment' => 'root url with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.',
            ),
            'FILE_DIR' => array(
                'value' => 'file/',
                'comment' => 'uploded files directory',
            ),
            'TMP_FILE_DIR' => array(
                'value' => 'file/tmp/',
                'comment' => 'temporary files directory',
            ),
            'FILE_REPOSITORY_DIR' => array(
                'value' => 'file/repository/',
                'comment' => 'files repository.',
            ),
            'SECURE_DIR' => array(
                'value' => 'file/secure/',
                'comment' => 'directory not accessible from the Internet',
            ),
            'TMP_SECURE_DIR' => array(
                'value' => 'file/secure/tmp/',
                'comment' => 'directory for temporary files. Not accessible from the Internet.',
            ),
            'MANUAL_DIR' => array(
                'value' => 'file/manual/',
                'comment' => 'Used for TinyMCE file browser and others tools where user manually controls all files.',
            ),
            'DEVELOPMENT_ENVIRONMENT' => array(
                'value' => 1,
                'comment' => 'displays error and debug information. Change to 0 before deployment to production server',
            ),
            'ERRORS_SHOW' => array(
                'value' => 1,
                'comment' => "0 if you don't wish to display errors on the page",
            ),
            'ERRORS_SEND' => array(
                'value' => '',
                'comment' => 'insert email address or leave blank. If email is set, you will get an email when an error occurs.',
            ),
            // END GLOBAL

            // BACKEND
            'INCLUDE_DIR' => array(
                'value' => 'ip_cms/includes/',
                'comment' => 'system directory',
            ),
            'LIBRARY_DIR' => array(
                'value' => 'ip_libs/',
                'comment' => 'general classes and third party libraries',
            ),
            'MODULE_DIR' => array(
                'value' => 'ip_cms/modules/',
                'comment' => 'system modules directory',
            ),
            'pluginDir' => array(
                'value' => './Plugin',
                'comment' => 'Plugins directory',
            ),
            'THEME_DIR' => array(
                'value' => 'ip_themes/',
                'comment' => 'themes directory',
            ),
            'BACKEND_MAIN_FILE' => array(
                'value' => 'admin.php',
                'comment' => 'backend root file',
            ),
            // END BACKEND

            // FRONTEND
            'CHARSET' => array(
                'value' => 'UTF-8',
                'comment' => 'system characterset',
            ),
            'THEME' => array(
                'value' => 'Blank',
                'comment' => 'theme from themes directory',
            ),
            'DEFAULT_DOCTYPE' => array(
                'value' => 'DOCTYPE_HTML5',
                'comment' => 'look ip_cms/includes/Ip/View.php for available options.'
            ),
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
                'comment' => 'Database configuration',
            ),
            // END DB
        );

        // Override template values:
        foreach ($configInfo as $key => $info) {
            if (array_key_exists($key, $config)) {
                $configInfo[$key]['value'] = $config[$key];
            }
        }

        // Generate config code:
        $configCode = "";
        foreach ($configInfo as $key => $info) {
            $configCode.= "\n    '{$key}' => " . var_export($info['value'], true) . ",";
            if (!empty($info['comment'])) {
                $configCode.= " // " . $info['comment'];
            }
        }

        $configCode = "<"."?php

/**
 * @package ImpressPages
 */

 return array(" . $configCode . ");";

        file_put_contents($filename, $configCode);
    }

    public static function writeRobotsFile($filename)
    {
        $content =
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
Sitemap: '. \Ip\Config::baseUrl('sitemap.php');

        file_put_contents($filename, $content);
    }

    public static function importParameters()
    {
//        define('BASE_DIR', get_parent_dir());
//        define('BACKEND', 1);
//        define('INCLUDE_DIR', 'ip_cms/includes/');
//        define('MODULE_DIR', 'ip_cms/modules/');
//        define('LIBRARY_DIR', 'ip_libs/');
//        define('DB_PREF', $_POST['prefix']);
//        define('THEME', 'Blank');
//        define('THEME_DIR', 'ip_themes/');

        require_once \Ip\Config::includePath('parameters.php');
        require \Ip\Config::baseFile('install/themeParameters.php');
        require_once \Ip\Config::baseFile('ip_cms/modules/developer/localization/manager.php');

        global $parametersMod;
        $parametersMod = new parametersMod();

        \Modules\developer\localization\Manager::saveParameters(\Ip\Config::baseFile('install/parameters.php'));

        \Modules\developer\localization\Manager::saveParameters(\Ip\Config::baseFile('install/themeParameters.php'));
    }

    public static function insertAdmin($user, $pass)
    {
        $sql = "UPDATE `" .\Ip\Db::tablePrefix() . "user` SET `name` = ?, `pass` = ? limit 1";
        // TODOX use salt for passwords
        \Ip\Db::execute($sql, array($user, md5($pass)));
    }

    public static function setSiteName($siteName)
    {
        $sql = "update `".\Ip\Db::tablePrefix()."par_lang` set `translation` = REPLACE(`translation`, '[[[[site_name]]]]', ?)";
        \Ip\Db::execute($sql, array($siteName));
    }

    public static function setSiteEmail($siteEmail)
    {
        $sql = "update `".\Ip\Db::tablePrefix() . "par_lang` set `translation` = REPLACE(`translation`, '[[[[site_email]]]]', ?)";
        \Ip\Db::execute($sql, array($siteEmail));
    }

}