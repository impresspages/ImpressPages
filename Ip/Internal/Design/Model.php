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
        return ipThemeFile('plugins/');
    }



    public function getThemePlugins()
    {
        //TODOX refactor to new plugins
        if (!is_dir(ipFile($this->getThemePluginDir()))) {
            return array();
        }

        $pluginConfigs = array();

        $groups = scandir($this->getThemePluginDir());
        foreach ($groups as $group) {
            $groupDir = ipFile($this->getThemePluginDir() . $group);
            if (is_dir($groupDir) && $group[0] != '.') {//don't add slash before is_dir check as it throws open basedir error
                $groupDir .= '/';
                $plugins = scandir($groupDir);
                foreach ($plugins as $plugin) {
                    $pluginDir = $groupDir . $plugin;
                    if (is_dir($pluginDir) && $plugin[0] != '.') { //don't add slash before is_dir check as it throws open basedir error
                        $pluginDir .= '/';
                        $pluginConfiguration = \Modules\developer\modules\Service::parsePluginConfig($pluginDir);
                        if ($pluginConfiguration) {
                            $pluginConfigs[] = $pluginConfiguration;
                        }
                    }
                }
            }
        }
        return $pluginConfigs;

    }

    public function installThemePlugin($pluginName)
    {
        //refactor to new plugins
        // TODOX Plugin dir
        $toDir = ipFile('Plugin/' . $pluginName . '/');
        $fromDir = ipFile('Plugin/' . $pluginName . '/');

        if (is_dir($toDir)) {
            throw new \Exception('This plugin has been already installed');
        }

        if (!is_dir($fromDir)) {
            throw new \Exception('This plugin has been already installed.');
        }

        $pluginConfiguration = \Modules\developer\modules\Service::parsePluginConfig($fromDir);

        if (!$pluginConfiguration) {
            throw new \Exception('Can\'t read plugin configuration file.');
        }

        if (!is_writable(ipFile('Plugin/'))) {
            throw new \Exception('Please make plugin dir writable (' . $this->getThemePluginDir() . ')');
        }

        $helper = Helper::instance();
        $helper->cpDir($fromDir, $toDir);
        \Modules\developer\modules\Service::installPlugin($pluginName);
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
        $cleanDirs = array_merge($cleanDirs, array(ipThemeFile('')));
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
            throw new \Ip\CoreException("Theme '" . $themeName . "' does not exist.");
        }
        $theme = $themes[$themeName];

        //TODOX new way of doing.
        $configModel = new \Ip\Internal\Config\Model();
        $configModel->changeConfigurationConstantValue('THEME', ipConfig()->theme(), $theme->getName());


        if (ipFile('Theme/' . $themeName . '/') != $theme->getPath()) {
            // TODOX add theme directory to override list
        }
//        $configModel->changeConfigurationConstantValue('THEME_DIR', ipConfig()->getRaw('THEME_DIR'), $theme->getPath());
        $configModel->changeConfigurationConstantValue('DEFAULT_DOCTYPE', ipConfig()->getRaw('DEFAULT_DOCTYPE'), $theme->getDoctype());


        $parametersFile = ipThemeFile('Theme/' . $themeName . '/'. Model::INSTALL_DIR . '/' . Model::PARAMETERS_FILE);
        if (file_exists($parametersFile)) {
            //TODOX new type of parameters

            \Modules\developer\localization\Manager::saveParameters($parametersFile);
        }

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
        if (defined('TEST_MARKET_URL')) {
            $marketUrl = TEST_MARKET_URL . 'themes-v1/';
        } else {
            $marketUrl = 'http://market.impresspages.org/themes-v1/';
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
        if ($dir) {
            $currentThemeDir = ipThemeFile('');
            // TODOX add theme override to config
            ipConfig()->_changeCore('THEME_DIR', $dir);
        }

        $metadata = new ThemeMetadata();
        $metadata->setName($name);
        if ($dir) {
            $metadata->setPath($dir);
        } else {
            $metadata->setPath(ipConfig()->getRaw('THEME_DIR'));
        }

        //old type config
        $themeIniFile = ipFile('Theme/' . $name . '/' . self::INSTALL_DIR . 'theme.ini');
        if (file_exists($themeIniFile)) {
            $iniConfig = $this->parseThemeIni($themeIniFile);
        } else {
            $iniConfig = array();
        }

        //new type config
        $themeJsonFile = ipFile('Theme/' . $name . '/' . self::INSTALL_DIR . 'Theme.json');
        if (file_exists($themeJsonFile)) {
            $jsonConfig = $this->parseThemeJson($themeJsonFile);
        } else {
            $themeJsonFile = ipFile('Theme/' . $name . '/' . self::INSTALL_DIR . 'theme.json');
            if (file_exists($themeJsonFile)) {
                $jsonConfig = $this->parseThemeJson($themeJsonFile);
            } else {
                $jsonConfig = array();
            }
        }

        $config = array_merge($iniConfig, $jsonConfig);

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

        if ($dir) {
            ipConfig()->_changeCore('THEME_DIR', $currentThemeDir);
        }

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
     * @throws \Ip\CoreException
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
