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
        }

        if ($this->rawConfig['BASE_DIR'] == '') {
            $this->rawConfig['BASE_DIR'] = dirname($_SERVER['SCRIPT_FILENAME']);
        }
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

    public function isDevelopmentEnvironment()
    {
        return !empty($this->rawConfig['DEVELOPMENT_ENVIRONMENT']);
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
            'BASE_URL' => '', //root url with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.

            'DEVELOPMENT_ENVIRONMENT' => 1, //displays error and debug information. Change to 0 before deployment to production server
            'ERRORS_SHOW' => 1,  //0 if you don't wish to display errors on the page
            'ERRORS_SEND' => '', //insert email address or leave blank. If email is set, you will get an email when an error occurs.
            // END GLOBAL

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