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
            return [];
        }

        $pluginConfigs = [];

        $plugins = scandir($this->getThemePluginDir());
        foreach ($plugins as $plugin) {
            $pluginDir = ipThemeFile('Plugin/' . $plugin);
            if (is_dir(
                    $pluginDir
                ) && $plugin[0] != '.' && $plugin[0] != '..'
            ) { //don't add slash before is_dir check as it throws open basedir error
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
        $themes = $this->getFolderThemes(ipFile('Theme/'));

        $themes = ipFilter('ipThemes', $themes);
        return $themes;
    }


    public function getThemeInstallDir()
    {
        return ipFile('Theme/');
    }


    /**
     * @param string $folder absolute path
     * @return array
     */
    protected function getFolderThemes($folder)
    {
        if (!is_dir($folder)) {
            return [];
        }
        $answer = [];
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


        ipEvent('ipBeforeThemeInstalled', array('themeName' => $themeName));

        \Ip\ServiceLocator::storage()->set('Ip', 'theme', $themeName);


        $service = Service::instance();
        $service->saveWidgetOptions($theme);


        //write down default theme options
        $options = $theme->getOptionsAsArray();
        foreach ($options as $option) {
            if (empty($option['name']) || empty($option['default'])) {
                continue;
            }

            $configModel = ConfigModel::instance();
            $newValue = $configModel->getConfigValue($themeName, $option['name'], $option['default']);
            $configModel->setConfigValue($themeName, $option['name'], $newValue);
        }

        ipEvent('ipThemeInstalled', array('themeName' => $themeName));


    }

    public function getMarketUrl()
    {
        if (ipGetOption('Ip.disableThemeMarket', 0)) {
            return '';
        }

        return ipConfig()->get('themeMarketUrl', ipConfig()->protocol() . '://market.impresspages.org/themes-v1/?version=4&cms=1');
    }


    /**
     * Read theme config and create theme entity
     * @param $name
     * @param null $dir
     * @param null $url
     * @return Theme
     * @throws \Exception
     */
    public function getTheme($name, $dir = null, $url = null)
    {

        if ($dir == null) {
            $dir = ipFile('Theme/');
        }
        $metadata = new ThemeMetadata();
        $metadata->setName($name);


        //new type config
        $themeJsonFile = $dir . $name . '/' . self::INSTALL_DIR . 'Theme.json';
        if (file_exists($themeJsonFile)) {
            $config = $this->parseThemeJson($themeJsonFile);
        } else {
            $themeJsonFile = $dir . $name . '/' . self::INSTALL_DIR . 'theme.json';
            if (file_exists($themeJsonFile)) {
                $config = $this->parseThemeJson($themeJsonFile);
            } else {
                $config = [];
            }
        }

        $config = ipFilter('ipThemeConfig', $config);

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

        if (!empty($url)) {
            $metadata->setUrl($url);
        }

        if (!empty($config['doctype']) && defined('\Ip\View::' . $config['doctype'])) {
            $metadata->setDoctype('DOCTYPE_' . $config['doctype']);
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
            return [];
        }

        $configJson = file_get_contents($file);

        $config = Helper::instance()->json_clean_decode($configJson, true);
        if ($config) {
            return $config;
        } else {
            return [];
        }
    }

    /**
     * Parse old style theme.ini file for theme configuration values
     */
    protected function parseThemeIni($file)
    {
        $answer = [];

        if (file_exists($file)) {
            $config = file($file);
            foreach ($config as $configRow) {
                $configName = substr($configRow, 0, strpos($configRow, ':'));
                $value = substr($configRow, strpos($configRow, ':') + 1);
                $value = str_replace("\n", "", str_replace("\r", "", $value));
                $answer[$configName] = $value;
            }
        } else {
            return [];
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
        $layouts = [];

        foreach ($files as $filename) {
            if ('php' == strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
                if ($filename[0] != '_') {
                    $file_contents = file_get_contents($themeDir.$filename);
                    preg_match_all("(@Layout Name:(.*)\n)siU", $file_contents, $file_layout);
                    if (isset($file_layout[1]) && isset($file_layout[1][0]) && !empty($file_layout[1][0])) {
                        $layout_name = preg_replace('/[^a-zA-Z0-9\s]/', '', $file_layout[1][0]);
                    } else {
                        $layout_name = '';
                    }
                    $layouts[] = array($filename, trim($layout_name) != '' ? $layout_name : $filename);
                }
            }
        }

        return $layouts;
    }

}
