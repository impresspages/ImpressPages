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

        if (empty($this->rawConfig['baseUrl'])) {
            $this->rawConfig['baseUrl'] = $server["SERVER_NAME"];

            if ($server["SERVER_PORT"] != "80") {
                $this->rawConfig['baseUrl'].= ":".$server["SERVER_PORT"];
            }

            $baseUrl = substr($server['SCRIPT_NAME'], 0, strrpos($server['SCRIPT_NAME'], '/') + 1);
            if (strpos($server['REQUEST_URI'], $baseUrl) !== 0) {
                throw new \Exception('Could not detect base URL. Please specify baseUrl in config.php');
            }

            $this->rawConfig['baseUrl'].= rtrim($baseUrl, '/') . '/';
        }

        if (empty($this->rawConfig['baseDir'])) {
            $this->rawConfig['baseDir'] = dirname($server['SCRIPT_FILENAME']);
        }

        if (empty($this->rawConfig['charset'])) {
            $this->rawConfig['charset'] = 'UTF-8';
        }

        if (empty($this->rawConfig['defaultDoctype'])) {
            $this->rawConfig['defaultDoctype'] = 'DOCTYPE_HTML5';
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
        return $this->protocol . $this->rawConfig['baseUrl'];
    }

    public function getRaw($name)
    {
        return array_key_exists($name, $this->rawConfig) ? $this->rawConfig[$name] : null;
    }

    //TODOXX refactor to removeDb #removeSetRaw
    public function _setRaw($name, $value)
    {
        if ($name == 'db' && $value !== null) {
            $this->tablePrefix = $value['tablePrefix'];
        }
        $this->rawConfig[$name] = $value;
    }

    public function isDevelopmentEnvironment()
    {
        return !empty($this->rawConfig['developmentEnvironment']);
    }


    public function isDebugMode()
    {
        return !empty($this->rawConfig['debugMode']);
    }

    public function theme()
    {
        if (!empty($this->rawConfig['theme'])) {
            return $this->rawConfig['theme'];
        } else {
            return ipStorage()->get('Ip', 'theme');
        }
    }


}
