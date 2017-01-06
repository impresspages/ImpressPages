<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Install;


class Helper
{

    public static $defaultLanguageCode = 'en';
    public static $firstStep = 1;
    public static $lastStep = 4;

    public static function getInstallationLanguages()
    {
        $languages = [];
        $languages['en'] = 'English';
        $languages['ar'] = 'Arabic';
        $languages['cn'] = 'Chinese';
        $languages['cs'] = 'Čeština';
        $languages['nl'] = 'Dutch';
        $languages['de'] = 'Deutsch';
        $languages['fr'] = 'French';
        $languages['it'] = 'Italiano';
        $languages['ja'] = '日本語';
        $languages['lt'] = 'Lietuvių';
        $languages['hu'] = 'Magyar';
//      $languages['pt'] = 'Portugues'; // Something is broken with translations.
        $languages['pl'] = 'Polski';
        $languages['ro'] = 'Română';
        $languages['ru'] = 'Русский';
        $languages['tr'] = 'Türkçe';

        return $languages;
    }

    public static function renderLayout($view, $data = [])
    {
        $content = ipView($view, $data)->render();

        $response = new LayoutResponse();
        $response->setLayout(ipFile('Ip/Internal/Install/view/layout.php'));
        $response->setContent($content);

        return $response;
    }

    public static function getTimezoneSelectOptions()
    {
        $dateTimeObject = new \DateTime();
        $currentTimeZone = (isset($_SESSION['config']) && !empty($_SESSION['config']['timezone']))? $_SESSION['config']['timezone'] : $dateTimeObject->getTimezone()->getName();
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
                $selected = ' selected="selected"';
            } else {
                $selected = '';
            }
            $timezoneSelectOptions .= '<option'.$selected.' value="'.addslashes($timezone).'">'.htmlspecialchars($timezone).'</option>';
        }

        return $timezoneSelectOptions;
    }

    /**
     * @param string $dir
     * @return bool
     */
    public static function isDirectoryWritable($dir)
    {
        $dir = rtrim($dir, '/\\');

        if (!is_writable($dir)) {
            return false;
        }

        $handle = opendir($dir);
        if (!$handle) {
            return false;
        }

        while (false !== ($file = readdir($handle))) {
            if ($file != ".." && !is_writable($dir . '/' . $file)) {
                closedir($handle);
                return false;
            }
        }

        closedir($handle);

        return true;
    }

    public static function isInstallAvailable()
    {
        if (!is_file("../config.php") || filesize("../config.php") !== false && filesize("../config.php") < 100) {
            return true;
        } else {
            return false;
        }
    }

    public static function generateMenu($curStep)
    {

        $steps = [];
//        $steps[] = __('Language selection', 'Install');
        $steps[1] = __('Configuration', 'Install');
        $steps[2] = __('System check', 'Install');
//        $steps[] = __('License', 'Install');
        $steps[3] = __('Database', 'Install');
        $steps[4] = __('Finish', 'Install');

        $answer = '
    <div class="list-group">
';

        foreach ($steps as $key => $step) {
            $class = "";
            if ($curStep >= $key) {
                $class = "success";
            }
            if ($key == $curStep) {
                $class = "active";
            }
            if ($key <= $curStep) {
                $answer .= '<a href="index.php?step=' . ($key) . '" class="list-group-item ' . $class . '">' . $step . '</a>';
            } else {
                $answer .= '<a href="#" class="list-group-item ' . $class . '">' . $step . '</a>';
            }

        }

        $answer .= '
    </div>
';

        return $answer;
    }

    public static function randString($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = '';
        $size = strlen( $chars );
        for( $i = 0; $i < $length; $i++ ) {
            $str .= $chars[ rand( 0, $size - 1 ) ];
        }

        return $str;
    }

    public static function validateWebsiteName($name)
    {
        if (!$name) {
            return false;
        }

        return true;
    }

    public static function validateWebsiteEmail($email)
    {
        if (!$email) {
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }

    public static function validateTimezone($timezone)
    {
        if (!$timezone) {
            return false;
        }

        return true;
    }

    public static function checkPhpVersion()
    {
        // this is checked in index.php
        return 'success';
    }

    public static function checkPDO()
    {
        if (!class_exists('PDO')) {
            return 'error';
        }
        return 'success';
    }


    public static function checkLibXml()
    {
        if (!function_exists('utf8_decode')) {
            return 'error';
        }
        return 'success';
    }


    public static function checkGD()
    {
        if (!extension_loaded('gd') || !function_exists('gd_info')) {
            return 'error';
        }
        return 'success';
    }

    public static function checkPhpSessions()
    {
        if (session_id() == '') { // session hasn't been started
            return 'warning';
        }
        // todox: create algorithm that is reliable and can tell 100% that sessions don't work; then throw 'error'
        return 'success';
    }

    public static function checkFileDotHtaccess()
    {
        if (!file_exists(ipConfig()->get('baseDir') . '/.htaccess')) {
            return 'error';
        }
        return 'success';
    }

    public static function checkFileIndexDotHtml()
    {
        if (file_exists(ipConfig()->get('baseDir') . '/' . 'index.html')) {
            return 'error';
        }
        return 'success';
    }

    public static function checkModRewrite()
    {
        if (empty($_SESSION['rewritesEnabled'])) { // this test is done through ajax in first step
            // old way to test
            // if (function_exists('apache_get_modules'))
            //    if (!in_array('mod_rewrite', apache_get_modules())
            return 'warning';
        }
        return 'success';
    }

    public static function checkCurl()
    {
        if (!function_exists('curl_init')) {
            return 'warning';
        }
        return 'success';
    }

    public static function checkMemoryLimit()
    {
        if (\Ip\Internal\System\Helper\SystemInfo::getMemoryLimitAsMb() < 100 && \Ip\Internal\System\Helper\SystemInfo::getMemoryLimitAsMb() != -1) {
            return 'warning';
        }
        return 'success';
    }

    public static function checkFolderFile()
    {
        if (!Helper::isDirectoryWritable(Model::ipFile('file/'))) {
            return 'error';
        }
        return 'success';
    }


    public static function checkFolderIp()
    {
        if (!Helper::isDirectoryWritable(ipConfig()->get('baseDir') . '/Ip/')) {
            return 'warning';
        }
        return 'success';
    }

    public static function checkFolderPlugin()
    {
        if (!Helper::isDirectoryWritable(ipFile('Plugin/'))) {
            return 'warning';
        }
        return 'success';
    }

    public static function checkFolderTheme()
    {
        if (!Helper::isDirectoryWritable(ipFile('Theme/'))) {
            return 'error';
        }
        return 'success';
    }

    public static function checkFileConfigPhp()
    {
        $configFile = ipConfig()->configFile();

        // if config.php file exists it should be writable
        if (is_file($configFile) && !is_writable($configFile)) {
            return 'error';
        }
        // if config.php file doesn't exist we should be able to create it
        if (!is_file($configFile) && !is_writable(dirname($configFile))) {
            return 'error';
        }
        return 'success';
    }

    public static function testDBTables($prefix)
    {
        $tableExists = false;

        $tables = array(
            'page',
            'page_storage',
            'permission',
            'language',
            'log',
            'email_queue',
            'repository_file',
            'repository_reflection',
            'widget',
            'widget_order',
            'theme_storage',
            'inline_value_global',
            'inline_value_language',
            'inline_value_page',
            'plugin',
            'storage',
            'revision',
            'administrator'
        );

        foreach ($tables as $table) {
            try {
                $sql = 'SELECT 1 FROM `' . $prefix . $table . '`';
                ipDb()->execute($sql);
                $tableExists = true;
            } catch (\Exception $e) {
                // Do nothing. We have expected this error to occur. That means the database is clean.
            }
        }

        return $tableExists;
    }

    public static function setUsageStatistics($action, $data)
    {
        $usageStatistics = array(
            'action' => $action,
            'data' => $data,
            'websiteId' => $_SESSION['websiteId'],
            'plugins' => [],
            'languages' => [],
            'pages' => [],
            'locale' => isset($_SESSION['installationLanguage']) ? $_SESSION['installationLanguage'] : \Ip\Internal\Install\Helper::$defaultLanguageCode,
            'doSupport' => !empty($_SESSION['config']['support']),
            'administrators' => array(array(
                'id' => 'install',
                'email' => $_SESSION['config']['websiteEmail'],
                'permissions' => array('install' => 'install')
            )),
            'themes' => array(
                'active' => ipConfig()->theme(),
                'all' => null
            )
        );

        return $usageStatistics;
    }

    public static function isApache()
    {
        return stripos($_SERVER['SERVER_SOFTWARE'], 'apache') !== false;
    }

    public static function isNginx()
    {
        return stripos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false;
    }
}
