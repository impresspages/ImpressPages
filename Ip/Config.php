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

    public function __construct($config)
    {
        $this->rawConfig = $config;

        // TODOX remove
        if (!defined('DB_PREF')) {
            define('DB_PREF', $this->rawConfig['db']['tablePrefix']);
        }

        $this->core['CORE_DIR'] = $this->rawConfig['BASE_DIR'] . $this->rawConfig['CORE_DIR'];
        $this->core['THEME_DIR'] = $this->rawConfig['BASE_DIR'] . $this->rawConfig['THEME_DIR'];

        if (isset($this->_SERVER["HTTPS"]) && $this->_SERVER["HTTPS"] == "on") { // TODOX fix error
            $this->protocol = 'https://';
        } else {
            $this->protocol = 'http://';
        }
//TODOX ask Algimantas if this comment is still useful
//        $relativeDirs = array(
//            'fileDir',
//            'pluginDir',
//        );
//
//        foreach ($relativeDirs as $relativeDir) {
//            if (self::$rawConfig[$relativeDir][0] == '.') {
//                self::$config[$relativeDir] = self::$rawConfig['baseDir'] . substr(self::$rawConfig[$relativeDir], 1);
//            } else {
//                self::$config[$relativeDir] = self::$rawConfig[$relativeDir];
//            }
//        }
//
//        self::$config['homeUrl'] = self::$rawConfig['protocol'] . '://' . $this->rawConfig['host'] . $this->rawConfig['siteUrlPath'];
    }



    public function coreModuleUrl($path)
    {
        return $this->protocol . $this->rawConfig['BASE_URL'] . 'Ip/Module/' . $path;
    }

    public function coreModuleFile($path)
    {
        return $this->rawConfig['BASE_DIR'] . 'Ip/Module/' . $path;
    }

    public function getRaw($name)
    {
        return array_key_exists($name, $this->rawConfig) ? $this->rawConfig[$name] : null;
    }

    //TODOX ask Algimantas what does the underscore mean
    public function _setRaw($name, $value)
    {
        $this->rawConfig[$name] = $value;
    }

    public function getCore($name)
    {
        return $this->core[$name];
    }

    public function _changeCore($name, $value)
    {
        // TODO do this through events
        $this->core[$name] = $value;
    }

    public function libraryUrl($path)
    {
        return $this->protocol . $this->rawConfig['BASE_URL'] . $this->rawConfig['LIBRARY_DIR'] . $path;
    }

    public function libraryFile($path)
    {
        return $this->rawConfig['BASE_DIR'] . $this->rawConfig['LIBRARY_DIR'] . $path;
    }

    public function temporaryFile($path)
    {
        return $this->rawConfig['BASE_DIR'] . $this->rawConfig['TMP_FILE_DIR'] . $path;
    }

    public function temporarySecureFile($path)
    {
        return $this->rawConfig['BASE_DIR'] . $this->rawConfig['TMP_SECURE_DIR'] . $path;
    }

    public function themeUrl($path)
    {
        return $this->protocol . $this->rawConfig['BASE_URL'] . $this->rawConfig['THEME_DIR'] . $this->rawConfig['THEME'] . '/' . $path;
    }

    public function themeFile($path, $theme = null)
    {
        if (!$theme) {
            $theme = $this->rawConfig['THEME'];
        }

        return $this->rawConfig['BASE_DIR'] . $this->rawConfig['THEME_DIR'] . $theme . '/' . $path;
    }

    public function coreUrl($path)
    {
        return $this->protocol . $this->rawConfig['BASE_URL'] . $this->rawConfig['CORE_DIR'] . $path;
    }

    public function coreFile($path)
    {
        return $this->rawConfig['BASE_DIR'] . $this->rawConfig['CORE_DIR'] . $path;
    }



    public function fileDirFile($path)
    {
        return $this->rawConfig['BASE_DIR'] . $this->rawConfig['FILE_DIR'] . $path;
    }

    public function repositoryFile($path)
    {
        return $this->rawConfig['BASE_DIR'] . $this->rawConfig['FILE_REPOSITORY_DIR'] . $path;
    }

    public function isDevelopmentEnvironment()
    {
        return !empty($this->rawConfig['DEVELOPMENT_ENVIRONMENT']);
    }

    public function baseUrl($path, $query = array(), $querySeparator = '&')
    {
        $url = $this->protocol . $this->rawConfig['BASE_URL'] . $path;
        if ($query) {
            $url .= '?' . http_build_query($query, null, $querySeparator);
        }

        return $url;
    }

    public function baseFile($path)
    {
        return $this->rawConfig['BASE_DIR'] . $path;
    }

    public function pluginFile($path)
    {
        return $this->rawConfig['BASE_DIR'] . $this->rawConfig['PLUGIN_DIR'] . $path;
    }

    public function pluginUrl($path)
    {
        return $this->protocol . $this->rawConfig['BASE_URL'] . $this->rawConfig['PLUGIN_DIR'] . $path;
    }

    public function theme()
    {
        return $this->rawConfig['THEME'];
    }


}