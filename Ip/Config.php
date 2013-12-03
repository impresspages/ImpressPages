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

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
            $this->protocol = 'https://';
        } else {
            $this->protocol = 'http://';
        }

    }

    public function baseUrl()
    {
        return $this->protocol . $this->rawConfig['BASE_URL'];
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


}