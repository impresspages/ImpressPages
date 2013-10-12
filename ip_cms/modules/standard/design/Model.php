<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\standard\design;


use \Modules\developer\form as Form;

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
        return THEME_DIR . THEME . '/plugins/';
    }



    public function getThemePlugins()
    {
        if (!is_dir($this->getThemePluginDir())) {
            return array();
        }

        $pluginConfigs = array();

        $groups = scandir($this->getThemePluginDir());

        foreach ($groups as $group) {
            $groupDir = $this->getThemePluginDir() . $group . '/';
            if (is_dir($groupDir) && $group[0] != '.') {
                $plugins = scandir($groupDir);
                foreach ($plugins as $plugin) {
                    $pluginDir = $groupDir . $plugin . '/';
                    if (is_dir($pluginDir) && $plugin[0] != '.') {
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

    public function installThemePlugin($pluginGroup, $pluginName)
    {
        $toDir = BASE_DIR . PLUGIN_DIR . $pluginGroup . '/' . $pluginName . '/';
        $fromDir = $this->getThemePluginDir() . $pluginGroup . '/' . $pluginName . '/';

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

        if (!is_writable(BASE_DIR . PLUGIN_DIR)) {
            throw new \Exception('Please make plugin dir writable (' . $this->getThemePluginDir() . ')');
        }

        if (!is_dir(BASE_DIR . PLUGIN_DIR . $pluginGroup)) {
            mkdir(BASE_DIR . PLUGIN_DIR . $pluginGroup);
        }
        $helper = Helper::instance();
        $helper->cpDir($fromDir, $toDir);
        \Modules\developer\modules\Service::installPlugin($pluginGroup, $pluginName);


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
        $parametersMod = \Ip\ServiceLocator::getParametersMod();

        $cleanDirs = array();

        $optionDirs = $parametersMod->getValue('standard', 'design', 'options', 'theme_dirs');
        $optionDirs = str_replace(array("\r\n", "\r"), "\n", $optionDirs);
        $lines = explode("\n", $optionDirs);
        foreach ($lines as $line) {
            if(!empty($line))
                $cleanDirs[] = trim($line);
        }
        $cleanDirs = array_merge($cleanDirs, array(THEME_DIR));
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
                    $answer[$file] = $this->getTheme($folder, $file);
                }
            }
            closedir($handle);
        }

        return $answer;
    }

    public function isThemeAvailable($name)
    {
        $themeDir = BASE_DIR . THEME_DIR . $name;
        return is_dir($themeDir);
    }


    public function installTheme($themeName)
    {
        $themes = $this->getAvailableThemes();
        if (!isset($themes[$themeName])) {
            throw new \Ip\CoreException("Theme '" . $themeName . "' does not exist.");
        }
        $theme = $themes[$themeName];

        $configModel = new \Modules\standard\configuration\Model();
        $configModel->changeConfigurationConstantValue('THEME', THEME, $theme->getName());
        $configModel->changeConfigurationConstantValue('THEME_DIR', THEME_DIR, $theme->getPath());
        $configModel->changeConfigurationConstantValue('DEFAULT_DOCTYPE', DEFAULT_DOCTYPE, $theme->getDoctype());


        $parametersFile = BASE_DIR . THEME_DIR . $themeName . '/' . Model::INSTALL_DIR . '/' . Model::PARAMETERS_FILE;
        if (file_exists($parametersFile)) {
            if (!defined('BACKEND')) {
                define('BACKEND', TRUE);
            }
            require_once(BASE_DIR . MODULE_DIR . 'developer/localization/manager.php');
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

        \DbSystem::setSystemVariable('theme_changed', time());

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
    public function getTheme($dir, $name)
    {
        $metadata = new ThemeMetadata();
        $metadata->setName($name);
        $metadata->setPath($dir);

        //old type config
        $themeIniFile = $dir . $name . '/' . self::INSTALL_DIR . 'theme.ini';
        if (file_exists($themeIniFile)) {
            $iniConfig = $this->parseThemeIni($themeIniFile);
        } else {
            $iniConfig = array();
        }

        //new type config
        $themeJsonFile = $dir . $name . '/' . self::INSTALL_DIR . 'Theme.json';
        if (file_exists($themeJsonFile)) {
            $jsonConfig = $this->parseThemeJson($themeJsonFile);
        } else {
            $themeJsonFile = $dir . $name . '/' . self::INSTALL_DIR . 'theme.json';
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
            foreach ($config as $key => $configRow) {
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


}