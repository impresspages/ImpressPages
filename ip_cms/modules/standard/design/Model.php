<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\standard\design;


use \Modules\developer\form as Form;

class Model
{

    const INSTALL_DIR = 'install/';
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

    /**
     * @return Theme[]
     */
    public function getAvailableThemes()
    {
        $answer = array();
        if ($handle = opendir(BASE_DIR . THEME_DIR)) {
            while (false !== ($file = readdir($handle))) {
                if (is_dir(BASE_DIR . THEME_DIR . $file) && $file != '..' && $file != '.' && substr(
                        $file,
                        0,
                        1
                    ) != '.'
                ) {
                    $answer[] = $this->getTheme($file);
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
        $availableThemes = self::getAvailableThemes();
        $theme = null;
        foreach ($availableThemes as $availableTheme) {
            if ($availableTheme->getName() == $themeName) {
                $theme = $availableTheme;
                break;
            }
        }

        if (!$theme) {
            throw new \Exception("Theme '" . $themeName . "' does not exist.");
        }

        $configModel = new \Modules\standard\configuration\Model();
        $configModel->changeConfigurationConstantValue('THEME', THEME, $theme->getName());
        $configModel->changeConfigurationConstantValue('DEFAULT_DOCTYPE', DEFAULT_DOCTYPE, $theme->getDoctype());

        $parametersFile = BASE_DIR . THEME_DIR . $themeName . '/' . Theme::INSTALL_DIR . '/' . Theme::PARAMETERS_FILE;
        if (file_exists($parametersFile)) {
            require_once(BASE_DIR . MODULE_DIR . 'developer/localization/manager.php');
            \Modules\developer\localization\Manager::saveParameters($parametersFile);
        }

        \DbSystem::setSystemVariable('theme_changed', time());

    }

    public function getMarketUrl()
    {
        if (defined('TEST_MARKET_URL')) {
            $marketUrl = TEST_MARKET_URL . 'en/themes-v1/';
        } else {
            $marketUrl = 'http://market.impresspages.org/en/themes-v1/';
        }
        return $marketUrl;
    }

    /**
     * Read theme config and create theme entity
     * @param $name
     * @return Theme
     */
    public function getTheme($name)
    {
        $metadata = new ThemeMetadata();
        $metadata->setName($name);

        //old type config
        $themeIniFile = BASE_DIR . THEME_DIR . $name . '/' . self::INSTALL_DIR . 'theme.ini';
        if (file_exists($themeIniFile)) {
            $iniConfig = $this->parseThemeIni($themeIniFile);
        } else {
            $iniConfig = array();
        }

        //new type config
        $themeJsonFile = BASE_DIR . THEME_DIR . $name . '/' . self::INSTALL_DIR . 'theme.json';
        if (file_exists($themeJsonFile)) {
            $jsonConfig = $this->parseThemeJson($themeJsonFile);
        } else {
            $jsonConfig = array();
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
            $metadata->setDoctype($config['doctype']);
        } else {
            $metadata->setDoctype('DOCTYPE_HTML5');
        }

        if (!empty($config['options'])) {
            $metadata->setOptions($config['options']);
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
        $config = json_decode($configJson, true);
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