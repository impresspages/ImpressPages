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
    protected $protocolUrl;
    protected $protocol;
    protected $isConfigEmpty;

    /**
     * @param $config
     * @param array|null $server $_SERVER
     */
    public function __construct($config, $server = null)
    {
        $config = $this->parseConfig($config);


        $this->isConfigEmpty = empty($config);
        $this->config = $config;

        if (!empty($config['db'])) {
            if (!isset($this->config['tablePrefix'])) {
                $this->config['tablePrefix'] = $this->config['db']['tablePrefix'];
            }
            if (!isset($this->config['database'])) {
                $this->config['database'] = $this->config['db']['database'];
            }
        } else {
            $this->config['database'] = null;
        }

        if (!$server) {
            $server = $_SERVER;
        }

        if (empty($this->config['baseUrl'])) {
            $this->config['baseUrl'] = $server["HTTP_HOST"];

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

            $this->config['baseUrl'] .= rtrim($baseUrl, '/') . '/';
        }

        if (empty($this->config['baseDir'])) {
            $this->config['baseDir'] = realpath(getcwd());
            if (DIRECTORY_SEPARATOR  === '\\') {
                $this->config['baseDir'] = str_replace('\\', '/', $this->config['baseDir']); //windows support both slashes. So make them Linux style.
            }
        }

        if (empty($this->config['coreDir'])) {
            if ($this->isComposerCore()) {
                $this->config['coreDir'] = realpath(dirname(getcwd()) . '/vendor/impresspages/impresspages');
            } else {
                $this->config['coreDir'] = realpath(getcwd());
            }
            if (DIRECTORY_SEPARATOR  === '\\') {
                $this->config['coreDir'] = str_replace('\\', '/', $this->config['coreDir']); //windows support both slashes. So make them Linux style.
            }
        }

        if (empty($this->config['charset'])) {
            $this->config['charset'] = 'UTF-8';
        }

        if (empty($this->config['defaultDoctype'])) {
            $this->config['defaultDoctype'] = 'DOCTYPE_HTML5';
        }


        if ((isset($server['HTTPS']) && strtolower($server['HTTPS']) == "on") || strtolower(getenv('HTTP_X_FORWARDED_PROTO')) === 'https') {
            $this->protocol = 'https';
        } else {
            $this->protocol = 'http';
        }

        $this->config['composerPlugins'] = [];
        $this->config['composerPluginPaths'] = [];
        if ($this->isComposerCore()) {
            $composerConfigFile = dirname(getcwd()) . '/composerPlugins.php';
            if (is_file($composerConfigFile)) {
                $composerPlugins = require($composerConfigFile);
                $this->config['composerPlugins'] = $composerPlugins;

                foreach ($composerPlugins as $plugin => $path) {
                    $this->config['composerPluginPaths'][$path] = $plugin;
                }
            }
        }

        $this->protocolUrl = $this->protocol . '://';
    }

    /**
     *
     */
    public function protocol()
    {
        return $this->protocol;
    }

    public function database()
    {
        return $this->config['database'];
    }

    public function tablePrefix()
    {
        return $this->config['tablePrefix'];
    }

    /**
     * Returns absolute base url.
     * @param string $protocol 'http:// https:// or //. Current protocol will be used if null
     * @return string
     */
    public function baseUrl($protocol = null)
    {
        $prot = $this->protocolUrl;
        if ($protocol !== null) {
            $prot = $protocol;
        }
        return $prot . $this->config['baseUrl'];
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
            if (!empty($value['database'])) {
                $this->set('database', $value['database']);
            }
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
            if (!ipConfig()->database()) {
                return '';
            }
            return ipStorage()->get('Ip', 'theme');
        }
    }

    public function adminLocale()
    {
        if (!empty($_COOKIE["ipAdminLocale"])) {
            return $_COOKIE["ipAdminLocale"];
        }
        if (!empty($this->config['adminLocale'])) {
            return $this->config['adminLocale'];
        }

        return 'en';
    }

    public function showErrors()
    {
        return !empty($this->config['showErrors']) || !empty($this->config['errorsShow']) || $this->isConfigEmpty;
    }

    public function isEmpty()
    {
        return $this->isConfigEmpty;
    }

    public function isComposerCore()
    {
        return is_dir(dirname(getcwd()) . '/vendor/impresspages/impresspages');
    }

    /**
     * @return string - path to the configuration file
     */
    public function configFile()
    {
        if ($this->isComposerCore()) {
            return dirname($this->get('baseDir')) . "/config.php";
        } else {
            return $this->get('baseDir') . "/config.php";
        }
    }

    private function parseConfig($configSetting)
    {
        if (is_array($configSetting)) {
            return $configSetting;
        }

        $configPath = $configSetting;

        $config = [];
        if ($configPath != null) {
            if (is_file($configPath)) {
                $configPath = dirname($configPath) . '/';
            }
        } else {
            if ($this->isComposerCore()) {
                $configPath = dirname(getcwd()) . '/';
            } else {
                $configPath = getcwd() . '/';
            }
        }

        $defaultConfigFile = $configPath . 'config.php';
        if (is_file($defaultConfigFile)) {
            $config = array_merge($config, require($defaultConfigFile));
        }

        $envConfigFile = $configPath . 'config-' . $this->getEnv() . '.php';
        if (is_file($envConfigFile)) {
            $config = array_merge($config, require($envConfigFile));
        }

        if (!is_array($config)) { //required for install, when config json parsing fails
            $config = [];
        }

        return $config;
    }

    protected function getEnv() {
        $environment = getenv('IP_ENV');
        if (!empty($environment)) {
            return $environment;
        }

        return 'dev';
    }
}
