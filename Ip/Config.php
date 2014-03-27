<?php
/**
 * @package ImpressPages
 */

namespace Ip;
/*
 * Core configuration
 *
 */

class Config
{
    protected $config;
    protected $protocol;

    /**
     * @param $config
     * @param array|null $server $_SERVER
     */
    public function __construct($config, $server = NULL)
    {
        $this->config = $config;

        if (!isset($this->config['tablePrefix'])) {
            $this->config['tablePrefix'] = $this->config['db']['tablePrefix'];
        }

        if (!$server) {
            $server = $_SERVER;
        }

        if (empty($this->config['baseUrl'])) {
            $this->config['baseUrl'] = $server["SERVER_NAME"];

            if ($server["SERVER_PORT"] != "80") {
                $this->config['baseUrl'].= ":".$server["SERVER_PORT"];
            }

            $baseUrl = substr($server['SCRIPT_NAME'], 0, strrpos($server['SCRIPT_NAME'], '/') + 1);
            if (DIRECTORY_SEPARATOR == '/') { // unix system
                if (strpos($server['REQUEST_URI'], $baseUrl) !== 0) {
                    // show instructions how to set baseUrl manually
                    include __DIR__ . '/Internal/Config/view/couldNotDetectBaseUrl.php';
                    exit();
                }
            } else { // windows system
                if (strpos(strtolower($server['REQUEST_URI']), strtolower($baseUrl)) !== 0) {
                    // show instructions how to set baseUrl manually
                    include __DIR__ . '/Internal/Config/view/couldNotDetectBaseUrl.php';
                    exit();
                }
            }

            $this->config['baseUrl'].= rtrim($baseUrl, '/') . '/';
        }

        if (empty($this->config['baseDir'])) {
            $this->config['baseDir'] = realpath(dirname($server['SCRIPT_FILENAME']));
        }

        if (empty($this->config['charset'])) {
            $this->config['charset'] = 'UTF-8';
        }

        if (empty($this->config['defaultDoctype'])) {
            $this->config['defaultDoctype'] = 'DOCTYPE_HTML5';
        }


        if (isset($server['HTTPS']) && $server['HTTPS'] == "on") {
            $this->protocol = 'https://';
        } else {
            $this->protocol = 'http://';
        }
    }

    public function tablePrefix()
    {
        return $this->config['tablePrefix'];
    }

    /**
     * Returns absolute base url.
     *
     * @return string
     */
    public function baseUrl()
    {
        return $this->protocol . $this->config['baseUrl'];
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if (array_key_exists($name, $this->config)) {
            return $this->config[$name];
        } elseif ($default instanceof \Closure) {
            /** @var $default \Closure */
            return $default();
        } else {
            return $default;
        }
    }

    public function set($name, $value)
    {
        if ($name == 'db' && $value) {
            $this->set('tablePrefix', $value['tablePrefix']);
        }

        if ($value === null) {
            unset($this->config[$name]);
        } else {
            $this->config[$name] = $value;
        }
    }

    public function isDevelopmentEnvironment()
    {
        return !empty($this->config['developmentEnvironment']);
    }


    public function isDebugMode()
    {
        return !empty($this->config['debugMode']);
    }

    public function theme()
    {
        if (!empty($this->config['theme'])) {
            return $this->config['theme'];
        } else {
            return ipStorage()->get('Ip', 'theme');
        }
    }

    public function showErrors()
    {
        return !empty($this->config['showErrors']) || !empty($this->config['errorsShow']);
    }

}
