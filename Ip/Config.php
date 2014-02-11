<?php
/**
 * @package ImpressPages
 */

namespace Ip;
/*
 * Core CMS configuration
 *
 */

class Config
{
    protected $rawConfig = array();
    protected $protocol = null;
    protected $corePath = null;
    protected $pluginUrl = null;
    protected $tablePrefix = null;

    /**
     * @param $config
     * @param array|null $server $_SERVER
     */
    public function __construct($config, $server = NULL)
    {
        $this->rawConfig = $config;

        $this->tablePrefix = $this->rawConfig['db']['tablePrefix'];

        if (!$server) {
            $server = $_SERVER;
        }

        if (empty($this->rawConfig['BASE_URL'])) {
            $this->rawConfig['BASE_URL'] = $server["SERVER_NAME"];

            if ($server["SERVER_PORT"] != "80") {
                $this->rawConfig['BASE_URL'].= ":".$server["SERVER_PORT"];
            }

            $baseUrl = substr($server['SCRIPT_NAME'], 0, strrpos($server['SCRIPT_NAME'], '/') + 1);
            if (strpos($server['REQUEST_URI'], $baseUrl) !== 0) {
                throw new \Exception('Could not detect BASE_URL. Please specify BASE_URL in config.php');
            }

            $this->rawConfig['BASE_URL'].= rtrim($baseUrl, '/') . '/';
        }

        if (empty($this->rawConfig['BASE_DIR'])) {
            $this->rawConfig['BASE_DIR'] = dirname($server['SCRIPT_FILENAME']);
        }

        if (empty($this->rawConfig['CHARSET'])) {
            $this->rawConfig['CHARSET'] = 'UTF-8';
        }

        if (empty($this->rawConfig['DEFAULT_DOCTYPE'])) {
            $this->rawConfig['DEFAULT_DOCTYPE'] = 'DOCTYPE_HTML5';
        }


        if (isset($server['HTTPS']) && $server['HTTPS'] == "on") {
            $this->protocol = 'https://';
        } else {
            $this->protocol = 'http://';
        }
    }

    public function tablePrefix()
    {
        return $this->tablePrefix;
    }

    public function setTablePrefix($tablePrefix)
    {
        $this->tablePrefix = $tablePrefix;
    }

    public function baseUrl()
    {
        return $this->protocol . $this->rawConfig['BASE_URL'];
    }

    public function getRaw($name)
    {
        return array_key_exists($name, $this->rawConfig) ? $this->rawConfig[$name] : null;
    }

    //TODOXX refactor to removeDb
    public function _setRaw($name, $value)
    {
        if ($name == 'db' && $value !== null) {
            $this->tablePrefix = $value['tablePrefix'];
        }
        $this->rawConfig[$name] = $value;
    }

    public function isDevelopmentEnvironment()
    {
        return !empty($this->rawConfig['DEVELOPMENT_ENVIRONMENT']);
    }


    public function isDebugMode()
    {
        return !empty($this->rawConfig['DEBUG_MODE']);
    }

    public function theme()
    {
        if (!empty($this->rawConfig['THEME'])) {
            return $this->rawConfig['THEME'];
        } else {
            return ipStorage()->get('Ip', 'theme');
        }
    }


}
