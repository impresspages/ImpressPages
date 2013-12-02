<?php
/**
 * @package ImpressPages
 */

namespace Ip;


class Config
{
    protected $rawConfig = array();
    protected $core = array();
    protected $protocol = null;
    protected $corePath = null;
    protected $pluginUrl = null;

    public function __construct($config)
    {
        $this->rawConfig = $config;

        // TODOX remove
        if (!defined('DB_PREF')) {
            define('DB_PREF', $this->rawConfig['db']['tablePrefix']);
        }

        if ($this->rawConfig['BASE_URL'] == '') {
            if ($_SERVER["SERVER_PORT"] != "80") {
                $this->rawConfig['BASE_URL'] = $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
            } else {
                $this->rawConfig['BASE_URL'] = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
            }
            //var_dump($_SERVER);
            var_dump($_GET);
        }

        if ($this->rawConfig['BASE_DIR'] == '') {
            $this->rawConfig['BASE_DIR'] = dirname($_SERVER['SCRIPT_FILENAME']);
            //$this->rawConfig['BASE_DIR'] = __DIR__
        }

        if (isset($this->_SERVER["HTTPS"]) && $this->_SERVER["HTTPS"] == "on") { // TODOX fix error
            $this->protocol = 'https://';
        } else {
            $this->protocol = 'http://';
        }

        if ($this->rawConfig['CORE_DIR']) {
            $this->corePath = $this->rawConfig['BASE_DIR'] . '/' . $this->rawConfig['CORE_DIR'];
        } else {
            $this->corePath = $this->rawConfig['BASE_DIR'];
        }

        if (empty($this->rawConfig['CDN_URL'])) {
            $this->cdnUrl = $this->rawConfig['BASE_URL'];
        } else {
            $this->cdnUrl = $this->rawConfig['CDN_URL'];
        }


        if ($this->rawConfig['PLUGIN_DIR']) {
            $this->pluginUrl = $this->protocol . $this->cdnUrl . '/' . $this->rawConfig['PLUGIN_DIR'] . '/Plugin';
            $this->pluginDir = $this->rawConfig['BASE_DIR'] . '/' . $this->rawConfig['PLUGIN_DIR'] . '/Plugin';
        } else {
            $this->pluginUrl = $this->protocol . $this->cdnUrl . '/Plugin';
            $this->pluginDir = $this->rawConfig['BASE_DIR'] . '/Plugin';
        }

        $this->core['THEME_DIR'] = $this->rawConfig['BASE_DIR'] . '/' . $this->rawConfig['THEME_DIR'];
    }



    public function __coreModuleUrl($path)
    {//echo $this->protocol . $this->rawConfig['BASE_URL']; exit;
        return $this->protocol . $this->cdnUrl . '/Ip/Module/' . $path;
    }

    public function coreModuleFile($path)
    {
        return $this->corePath . '/Ip/Module/' . $path;
    }

    public function getRaw($name)
    {
        return array_key_exists($name, $this->rawConfig) ? $this->rawConfig[$name] : null;
    }

    //TODOX refactor to removeDb
    public function _setRaw($name, $value)
    {
        $this->rawConfig[$name] = $value;
    }


    public function _changeCore($name, $value)
    {
        // TODO do this through events
        $this->core[$name] = $value;
    }



    public function temporaryFile($path)
    {
        return $this->rawConfig['BASE_DIR'] . '/' . $this->rawConfig['TMP_FILE_DIR'] . '/' . $path;
    }

    public function temporarySecureFile($path)
    {
        return $this->rawConfig['BASE_DIR'] . '/' . $this->rawConfig['TMP_SECURE_DIR'] . '/' . $path;
    }

    public function themeUrl($path)
    {
        return $this->protocol . $this->cdnUrl . '/' . $this->rawConfig['THEME_DIR'] . '/' . $this->rawConfig['THEME'] . '/' . $path;
    }

    public function themeFile($path, $theme = null)
    {
        if (!$theme) {
            $theme = $this->rawConfig['THEME'];
        }

        return $this->rawConfig['BASE_DIR'] . '/' . $this->rawConfig['THEME_DIR'] . '/' . $theme . '/' . $path;
    }

    //TODOX remove
    public function coreUrl($path)
    {
        return $this->protocol . $this->cdnUrl . '/' . $this->rawConfig['CORE_DIR'] . '/' . $path;
    }

    public function coreFile($path)
    {
        return $this->corePath . '/' . $path;
    }


    public function fileUrl($path)
    {
        return $this->protocol . $this->cdnUrl . '/' . $this->rawConfig['FILE_DIR'] . '/' . $path;
    }

    public function fileDirFile($path)
    {
        return $this->rawConfig['BASE_DIR'] . '/' . $this->rawConfig['FILE_DIR'] . '/' . $path;
    }

    public function repositoryFile($path)
    {
        return $this->rawConfig['BASE_DIR'] . '/' . $this->rawConfig['FILE_REPOSITORY_DIR'] . '/' . $path;
    }

    public function repositoryUrl($path)
    {
        return $this->protocol . $this->cdnUrl . '/' . $this->rawConfig['FILE_REPOSITORY_DIR'] . '/' . $path;
    }

    public function isDevelopmentEnvironment()
    {
        return !empty($this->rawConfig['DEVELOPMENT_ENVIRONMENT']);
    }

    public function baseUrl($path, $query = array(), $querySeparator = '&')
    {
        $url = $this->protocol . $this->rawConfig['BASE_URL'] . '/' . $path;
        if ($query) {
            $url .= '?' . http_build_query($query, null, $querySeparator);
        }

        return $url;
    }

    public function baseFile($path)
    {
        return $this->rawConfig['BASE_DIR'] . '/' . $path;
    }

    public function pluginFile($path)
    {
        return $this->pluginDir . '/' . $path;
    }

    public function pluginUrl($path)
    {
        return $this->pluginUrl . '/' . $path;
    }

    public function theme()
    {
        return $this->rawConfig['THEME'];
    }


    //TODOX REMOVE
    protected function defaultConfig()
    {
        $dateTimeObject = new DateTime();
        return array(
            // GLOBAL
            'SESSION_NAME' => 'xxx', //prevents session conflict when two sites runs on the same server
            // END GLOBAL

            // DB
            'db' => array(
                'hostname' => '',
                'username' => '',
                'password' => '',
                'database' => '',
                'tablePrefix' => '',
                'charset' => '',
            ),
            'DB_PREF' => '',
            // END DB

            // GLOBAL
            'BASE_DIR' => '', //root DIR with trailing slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
            'CORE_DIR' => '',
            'BASE_URL' => '', //root url with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
            'FILE_DIR' => 'file/', //uploaded files directory
            'TMP_FILE_DIR' => 'file/tmp/', //temporary files directory
            'FILE_REPOSITORY_DIR' => 'file/repository/', //files repository.
            'SECURE_DIR' => 'file/secure/', //directory not accessible from the Internet
            'TMP_SECURE_DIR' => 'file/secure/tmp/', //directory for temporary files. Not accessible from the Internet.
            'MANUAL_DIR' => 'file/manual/', //Used for TinyMCE file browser and others tools where user manually controls all files.

            'DEVELOPMENT_ENVIRONMENT' => 1, //displays error and debug information. Change to 0 before deployment to production server
            'ERRORS_SHOW' => 1,  //0 if you don't wish to display errors on the page
            'ERRORS_SEND' => '', //insert email address or leave blank. If email is set, you will get an email when an error occurs.
            // END GLOBAL

            // BACKEND
            'CONFIG_DIR' => 'ip_configs/', //modules configuration directory
            'PLUGIN_DIR' => 'Plugin/', //plugins directory
            'THEME_DIR' => 'Theme/', //themes directory

            // END BACKEND

            // FRONTEND
            'CHARSET' => 'UTF-8', //system character set
            'THEME' => 'Blank', //theme from themes directory
            'DEFAULT_DOCTYPE' => 'DOCTYPE_HTML5', //look ip_cms/includes/Ip/View.php for available options.

            'timezone' => $dateTimeObject->getTimezone()->getName(),
            // END FRONTEND

            'TEST_MODE' => 1,
            'TEST_MARKET_URL' => 'http://local.market.impresspages.org/'
            //define('IS_MOBILE', 1);


        );
    }
}