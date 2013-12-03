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

    /**
     * @param $config
     * @param array|null $server $_SERVER
     */
    public function __construct($config, $server = NULL)
    {
        $this->rawConfig = $config;

        // TODOX remove
        if (!defined('DB_PREF')) {
            define('DB_PREF', $this->rawConfig['db']['tablePrefix']);
        }

        if (!$server) {
            $server = $_SERVER;
        }

        if ($this->rawConfig['BASE_URL'] == '') {
            $this->rawConfig['BASE_URL'] = $server["SERVER_NAME"];

            if ($server["SERVER_PORT"] != "80") {
                $this->rawConfig['BASE_URL'].= ":".$server["SERVER_PORT"];
            }

            $baseUrl = dirname($server['SCRIPT_NAME']);
            if (strpos($server['REQUEST_URI'], $baseUrl) !== 0) {
                throw new \Exception('Could not detect BASE_URL. Please specify BASE_URL in ip_config.php');
            }

            $this->rawConfig['BASE_URL'].= ltrim($baseUrl, '/') . '/';
        }

        if ($this->rawConfig['BASE_DIR'] == '') {
            $this->rawConfig['BASE_DIR'] = dirname($server['SCRIPT_FILENAME']);
        }

        if (isset($server['HTTPS']) && $server['HTTPS'] == "on") {
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