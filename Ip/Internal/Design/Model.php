<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Design;


use \Ip\Form as Form;

class Model
{

    const INSTALL_DIR = 'setup/';
    const PARAMETERS_FILE = 'parameters.php';

    protected function __construct()
    {

    }

    /**
     * @return Model
     */
    public static function instance()
    {
        return new Model();
    }


    protected function getThemePluginDir()
    {
        return ipThemeFile('Plugin/');
    }



    public function getThemePlugins()
    {
        if (!is_dir($this->getThemePluginDir())) {
            return array();
        }

        $pluginConfigs = array();

        $plugins = scandir($this->getThemePluginDir());
        foreach ($plugins as $plugin) {
            $pluginDir = ipThemeFile('Plugin/' . $plugin);
            if (is_dir($pluginDir) && $plugin[0] != '.' && $plugin[0] != '..') { //don't add slash before is_dir check as it throws open basedir error
                $pluginDir .= '/';
                $pluginConfiguration = \Ip\Internal\Plugins\Service::parsePluginConfigFile($pluginDir);
                if ($pluginConfiguration) {
                    $pluginConfigs[] = $pluginConfiguration;
                }
            }
        }

        return $pluginConfigs;

    }

    public function installThemePlugin($pluginName)
    {
        $toDir = ipFile('Plugin/' . $pluginName . '/');
        $fromDir = ipThemeFile('Plugin/' . $pluginName . '/');

        if (is_dir($toDir)) {
            throw new \Ip\Exception('This plugin has been already installed');
        }

        if (!is_dir($fromDir)) {
            throw new \Ip\Exception('Plugin is missing.');
        }

        $pluginConfiguration = \Ip\Internal\Plugins\Service::parsePluginConfigFile($fromDir);

        if (!$pluginConfiguration) {
            throw new \Ip\Exception('Can\'t read plugin configuration file.');
        }

        if (!is_writable(ipFile('Plugin/'))) {
            throw new \Ip\Exception('Please make plugin dir writable (' . esc($this->getThemePluginDir()) . ')');
        }

        $helper = Helper::instance();
        $helper->cpDir($fromDir, $toDir);
        \Ip\Internal\Plugins\Service::activatePlugin($pluginName);
    }

    /**
     * @return Theme[]
     */
    public function getAvailableThemes()
    {
        $dirs = $this->getThemeDirs();
        $dirs = array_reverse($dirs); //first dir themes will override the themes from last ones
        $themes = array();
        foreach($dirs as $dir) {
            $themes = array_merge($themes, $this->getFolderThemes($dir));
        }
        return $themes;

    }

    /**
     * first dir themes will override the themes from last ones
     * @return array
     */
    protected function getThemeDirs()
    {
        //the order of dirs is very important. First dir themes overrides following ones.

        $cleanDirs = array();

        $optionDirs = ipGetOption('Design.themeDirs');
        $optionDirs = str_replace(array("\r\n", "\r"), "\n", $optionDirs);
        $lines = explode("\n", $optionDirs);
        foreach ($lines as $line) {
            if (!empty($line)) {
                $cleanDirs[] = trim($line);
            }
        }
        $cleanDirs = array_merge($cleanDirs, array(ipFile('Theme/')));
        return $cleanDirs;
    }

    public function getThemeInstallDir()
    {
        $themeDirs = $this->getThemeDirs();
        return array_shift($themeDirs);
    }


    /**
     * @param string $folder absolute path
     * @return array
     */
    protected function getFolderThemes($folder)
    {
        if (!is_dir($folder)) {
            return array();
        }
        $answer = array();
        if ($handle = opendir($folder)) {
            while (false !== ($file = readdir($handle))) {
                if (is_dir($folder . $file) && $file != '..' && $file != '.' && substr(
                        $file,
                        0,
                        1
                    ) != '.'
                ) {
                    $answer[$file] = $this->getTheme($file, $folder);
                }
            }
            closedir($handle);
        }

        return $answer;
    }

    public function isThemeAvailable($name)
    {
        return is_dir(ipFile('Theme/' . $name . '/'));
    }


    public function installTheme($themeName)
    {
        $themes = $this->getAvailableThemes();
        if (!isset($themes[$themeName])) {
            throw new \Ip\Exception("Theme '" . esc($themeName) . "' does not exist.");
        }
        $theme = $themes[$themeName];


        \Ip\ServiceLocator::storage()->set('Ip', 'theme', $themeName);


        $parametersFile = ipThemeFile('Theme/' . $themeName . '/'. Model::INSTALL_DIR . '/' . Model::PARAMETERS_FILE);

        $service = Service::instance();
        $service->saveWidgetOptions($theme);




        //write down default theme options
        $options = $theme->getOptionsAsArray();
        foreach($options as $option) {
            if (empty($option['name']) || empty($option['default'])) {
                continue;
            }

            $configModel = ConfigModel::instance();
            $newValue = $configModel->getConfigValue($themeName, $option['name'], $option['default']);
            $configModel->setConfigValue($themeName, $option['name'], $newValue);
        }

        \Ip\ServiceLocator::storage()->set('Ip', 'themeChanged', time());

    }

    public function getMarketUrl()
    {
        if (ipGetOption('Ip.disableThemeMarket', 0)) {
            return '';
        }

        if (ipConfig()->get('themeMarketUrl')) {
            $marketUrl = ipConfig()->get('themeMarketUrl') . 'themes-v1/?version=4';
        } elseif(ipConfig()->get('testMode')) {
            $marketUrl = 'http://local.market.impresspages.org/themes-v1/?version=4';
        } else {
            $marketUrl = 'http://market.impresspages.org/themes-v1/?version=4';
        }
        return $marketUrl;
    }

    /**
     * Read theme config and create theme entity
     * @param $name
     * @return Theme
     */
    public function getTheme($name, $dir = null)
    {

        $metadata = new ThemeMetadata();
        $metadata->setName($name);
        if ($dir) {
            $metadata->setPath($dir);
        } else {
            $metadata->setPath(ipFile('Theme'));
        }

        //new type config
        $themeJsonFile = ipFile('Theme/' . $name . '/' . self::INSTALL_DIR . 'Theme.json');
        if (file_exists($themeJsonFile)) {
            $config = $this->parseThemeJson($themeJsonFile);
        } else {
            $themeJsonFile = ipFile('Theme/' . $name . '/' . self::INSTALL_DIR . 'theme.json');
            if (file_exists($themeJsonFile)) {
                $config = $this->parseThemeJson($themeJsonFile);
            } else {
                $config = array();
            }
        }


        $metadata->setTitle(!empty($config['title']) ? $config['title'] : $name);

        if (!empty($config['author'])) {
            $metadata->setAuthorTitle($config['author']);
        }

        if (!empty($config['version'])) {
            $metadata->setVersion($config['version']);
        }

        if (!empty($config['thumbnail'])) {
            $metadata->setThumbnail($config['thumbnail']);
        }

        if (!empty($config['doctype']) && defined('\Ip\View::' . $config['doctype'])) {
            $metadata->setDoctype('DOCTYPE_'.$config['doctype']);
        } else {
            $metadata->setDoctype('DOCTYPE_HTML5');
        }

        if (!empty($config['options'])) {
            $metadata->setOptions($config['options']);
        }

        if (!empty($config['widget'])) {
            $metadata->setWidgetOptions($config['widget']);
        }


        $theme = new Theme($metadata);

        return $theme;
    }

    protected function parseThemeJson($file)
    {
        if (!file_exists($file) || !is_file($file)) {
            return array();
        }

        $configJson = file_get_contents($file);

        $config = Helper::instance()->json_clean_decode($configJson, true);
        if ($config) {
            return $config;
        } else {
            return array();
        }
    }

    /**
     * Parse old style theme.ini file for theme configuration values
     */
    protected function parseThemeIni($file)
    {
        $answer = array();

        if (file_exists($file)) {
            $config = file($file);
            foreach ($config as $configRow) {
                $configName = substr($configRow, 0, strpos($configRow, ':'));
                $value = substr($configRow, strpos($configRow, ':') + 1);
                $value = str_replace("\n", "", str_replace("\r", "", $value));
                $answer[$configName] = $value;
            }
        } else {
            return array();
        }
        return $answer;


    }



    /**
     * Returns possible layout pages.
     * files starting with underscore (for example, _layout.php) are considered hidden.
     * @return array layouts (e.g. ['main.php', 'home.php'])
     * @throws \Ip\Exception
     */
    public static function getThemeLayouts()
    {
        $themeDir = ipThemeFile('');
        $files = scandir($themeDir);
        $layouts = array();

        foreach ($files as $filename) {
            if ('php' == strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
                if ($filename[0] != '_') {
                    $layouts[] = $filename;
                }
            }
        }

        return $layouts;
    }

}
