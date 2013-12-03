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

        $answer = '<h1>' . __('System check', 'ipInstall') . "</h1>";

        $table = array();

        $table[] = __('PHP version >= 5.3', 'ipInstall');
        if (isset($error['php_version']))
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
        else
            $table[] = '<span class="correct">' . __('Ok', 'ipInstall') . '</span>';

        $table[] = __('Apache module "mod_rewrite"', 'ipInstall');
        if (isset($error['mod_rewrite']))
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
        else
            $table[] = '<span class="correct">' . __('Ok', 'ipInstall') . '</span>';


        $table[] = __('PHP module "PDO"', 'ipInstall');
        if (isset($error['mod_pdo']))
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
        else
            $table[] = '<span class="correct">' . __('Ok', 'ipInstall') . '</span>';

        $table[] = __('GD Graphics Library', 'ipInstall');
        if (isset($error['gd_lib']))
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
        else
            $table[] = '<span class="correct">' . __('Ok', 'ipInstall') . '</span>';

//sessions are checked using curl. If there is no curl, session availability hasn't been checked
        if (!isset($warning['curl'])) {
            $table[] = __('PHP sessions', 'ipInstall');
            if (isset($warning['session'])) {
                $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
            } else {
                $table[] = '<span class="correct">' . __('Ok', 'ipInstall') . '</span>';
            }
        }

        $table[] = __('.htaccess file', 'ipInstall');
        if (isset($error['htaccess']))
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
        else
            $table[] = '<span class="correct">' . __('Ok', 'ipInstall') . '</span>';


        $table[] = __('index.html removed', 'ipInstall');
        if (isset($error['index.html']))
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
        else
            $table[] = '<span class="correct">' . __('Ok', 'ipInstall') . '</span>';


        $table[] = __('Magic quotes off (optional)', 'ipInstall');
        if (isset($warning['magic_quotes']))
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
        else
            $table[] = '<span class="correct">' . __('Ok', 'ipInstall') . '</span>';

        $table[] = __('PHP module "Curl"', 'ipInstall');
        if (isset($warning['curl'])) {
            $table[] = '<span class="warning">' . __('Warning', 'ipInstall') . "</span>";
        } else {
            $table[] = '<span class="correct">' . __('Ok', 'ipInstall') . '</span>';
        }

        $table[] = sprintf( __('PHP memory limit (%s)', 'ipInstall'), ini_get('memory_limit'));
        if ((integer)ini_get('memory_limit') < 100) {
            $table[] = '<span class="warning">' . __('Warning', 'ipInstall') . "</span>";
        } else {
            $table[] = '<span class="correct">' . __('Ok', 'ipInstall') . "</span>";
        }


        $table[] = '';
        $table[] = '';


        $table[] = '';
        $table[] = '';


        $table[] = '<b>/file/</b> ' . __('writable', 'ipInstall') . ' ' . __('(including subfolders and files)', 'ipInstall');

        if (!Helper::isDirectoryWritable(ipFile('file/'))) {
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
            $error['writable_file'] = 1;
        } else
            $table[] = '<span class="correct">' . __('Ok', 'ipInstall') . '</span>';


        $table[] = '<b>/Theme/</b> ' . __('writable', 'ipInstall');
        if (!Helper::isDirectoryWritable(ipFile('Theme'))) {
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
            $error['writable_themes'] = 1;
        } else
            $table[] = '<span class="correct">' . __('Ok', 'ipInstall') . '</span>';


        $table[] = '<b>/ip_config.php</b> ' . __('writable', 'ipInstall');

        if (!is_writable(ipFile('ip_config.php'))) {
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
            $error['writable_config'] = 1;
        } else
            $table[] = '<span class="correct">' . __('Ok', 'ipInstall') . '</span>';


        $table[] = '<b>/robots.txt</b> ' . __('writable', 'ipInstall');
        if (!is_writable(ipFile('robots.txt'))) {
            $table[] = '<span class="error">' . __('No', 'ipInstall') . "</span>";
            $error['writable_robots'] = 1;
        } else
            $table[] = '<span class="correct">' . __('Ok', 'ipInstall') . '</span>';


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
            ipDb()->execute('USE `' . $database . '`');
        } catch (\PDOException $e) {
            try {
                ipDb()->execute("CREATE DATABASE `".$database."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
                ipDb()->execute('USE `' . $database . '`');
            } catch (\PDOException $e2) {
                throw new \Ip\CoreException('Could not create database');
            }
        }

        return true;
    }

    public static function createDatabaseStructure($database, $tablePrefix)
    {
        $all_sql = file_get_contents(ipFile('Plugin/Install/sql/structure.sql'));

        $all_sql = str_replace("[[[[database]]]]", $database, $all_sql);
        $all_sql = str_replace("TABLE IF EXISTS `ip_cms_", "TABLE IF EXISTS `". $tablePrefix, $all_sql);
        $all_sql = str_replace("TABLE IF NOT EXISTS `ip_cms_", "TABLE IF NOT EXISTS `".$tablePrefix, $all_sql);
        $sql_list = explode("-- Table structure", $all_sql);

        $errors = array();

        foreach ($sql_list as $sql) {
            try {
                ipDb()->execute($sql);
            } catch (\Exception $e) {
                $errors[] = preg_replace("/[\n\r]/", '', $sql . ' ' . Db::getConnection()->errorInfo());
            }
        }

        return $errors;
    }

    public static function importData($tablePrefix)
    {
        $errors = array();

        $sqlFile = ipFile('Plugin/Install/sql/data.sql');
        $fh = fopen($sqlFile, 'r');
        $all_sql = fread($fh, utf8_decode(filesize($sqlFile)));
        fclose($fh);

        // TODOX execute multiple statements
        //$all_sql = utf8_encode($all_sql);
        $all_sql = str_replace("INSERT INTO `ip_cms_", "INSERT INTO `". $tablePrefix, $all_sql);
        $all_sql = str_replace("[[[[base_url]]]]", ipConfig()->baseUrl(), $all_sql);
        $sql_list = explode("-- Dumping data for table--", $all_sql);


        foreach ($sql_list as $sql) {
            try {
                ipDb()->execute($sql);
            } catch (Exception $e) {
                $errors[] = preg_replace("/[\nipDb()->", "", $sql . ' ' . Db::getConnection()->errorInfo());
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
                'comment' => 'root DIR without trailing slash at the end. If you have moved your site to another place, change this line to correspond your new domain.',
            ),
            'BASE_URL' => array(
                'value' => '',
                'comment' => 'root url without trailing slash at the end. If you have moved your site to another place, change this line to correspond your new domain.',
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
            'pluginDir' => array(
                'value' => './Plugin',
                'comment' => 'Plugins directory',
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
                'comment' => "Database configuration",
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

    public static function writeRobotsFile($filename)
    {
        $content =
'User-agent: *
Disallow: /ip_configs/
Disallow: /update/
Disallow: /install/
Disallow: /admin.php
Disallow: /ip_config.php
Disallow: /ip_license.html
Disallow: /readme.md
Sitemap: '. ipFileUrl('sitemap.php');

        file_put_contents($filename, $content);
    }


    public static function insertAdmin($user, $pass)
    {
        $sql = "UPDATE `" .ipDb()->tablePrefix() . "user` SET `name` = ?, `pass` = ? limit 1";
        // TODOX use salt for passwords
        ipDb()->execute($sql, array($user, md5($pass)));
    }

    public static function setSiteName($siteName)
    {
        $sql = "update `".ipDb()->tablePrefix()."par_lang` set `translation` = REPLACE(`translation`, '[[[[site_name]]]]', ?)";
        ipDb()->execute($sql, array($siteName));
    }

    public static function setSiteEmail($siteEmail)
    {
        $sql = "update `".ipDb()->tablePrefix() . "par_lang` set `translation` = REPLACE(`translation`, '[[[[site_email]]]]', ?)";
        ipDb()->execute($sql, array($siteEmail));
    }

}