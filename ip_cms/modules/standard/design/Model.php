<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\standard\design;


use \Modules\developer\form as Form;

class Model
{

    const INSTALL_DIR = 'Setup/';
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
        $parametersMod = \Ip\ServiceLocator::getParametersMod();
        $theme = self::getTheme($themeName);



        if (!$theme) {
            throw new \Ip\CoreException("Theme '" . $themeName . "' does not exist.");
        }

        $configModel = new \Modules\standard\configuration\Model();
        $configModel->changeConfigurationConstantValue('THEME', THEME, $theme->getName());
        $configModel->changeConfigurationConstantValue('DEFAULT_DOCTYPE', DEFAULT_DOCTYPE, $theme->getDoctype());


        $parametersFile = BASE_DIR . THEME_DIR . $themeName . '/' . Theme::INSTALL_DIR . '/' . Theme::PARAMETERS_FILE;
        if (file_exists($parametersFile)) {
            if (!defined('BACKEND')) {
                define('BACKEND', TRUE);
            }
            require_once(BASE_DIR . MODULE_DIR . 'developer/localization/manager.php');
            \Modules\developer\localization\Manager::saveParameters($parametersFile);
        }

        $widgetOptions = $theme->getWidgetOptions();
        if (isset($widgetOptions['image']['width'])) {
            $parametersMod->setValue('standard', 'content_management', 'widget_image', 'width', $widgetOptions['image']['width']);
        }
        if (isset($widgetOptions['image']['height'])) {
            $parametersMod->setValue('standard', 'content_management', 'widget_image', 'height', $widgetOptions['image']['height']);
        }
        if (isset($widgetOptions['image']['bigWidth'])) {
            $parametersMod->setValue('standard', 'content_management', 'widget_image', 'big_width', $widgetOptions['image']['bigWidth']);
        }
        if (isset($widgetOptions['image']['bigHeight'])) {
            $parametersMod->setValue('standard', 'content_management', 'widget_image', 'big_height', $widgetOptions['image']['bigHeight']);
        }

        if (isset($widgetOptions['imageGallery']['width'])) {
            $parametersMod->setValue('standard', 'content_management', 'widget_image_gallery', 'width', $widgetOptions['imageGallery']['width']);
        }
        if (isset($widgetOptions['imageGallery']['height'])) {
            $parametersMod->setValue('standard', 'content_management', 'widget_image_gallery', 'height', $widgetOptions['imageGallery']['height']);
        }
        if (isset($widgetOptions['imageGallery']['bigWidth'])) {
            $parametersMod->setValue('standard', 'content_management', 'widget_image_gallery', 'big_width', $widgetOptions['imageGallery']['bigWidth']);
        }
        if (isset($widgetOptions['imageGallery']['bigHeight'])) {
            $parametersMod->setValue('standard', 'content_management', 'widget_image_gallery', 'big_height', $widgetOptions['imageGallery']['bigHeight']);
        }

        if (isset($widgetOptions['logoGallery']['width'])) {
            $parametersMod->setValue('standard', 'content_management', 'widget_logo_gallery', 'width', $widgetOptions['logoGallery']['width']);
        }
        if (isset($widgetOptions['logoGallery']['height'])) {
            $parametersMod->setValue('standard', 'content_management', 'widget_logo_gallery', 'height', $widgetOptions['logoGallery']['height']);
        }

        if (isset($widgetOptions['textImage']['width'])) {
            $parametersMod->setValue('standard', 'content_management', 'widget_text_image', 'width', $widgetOptions['textImage']['width']);
        }
        if (isset($widgetOptions['textImage']['height'])) {
            $parametersMod->setValue('standard', 'content_management', 'widget_text_image', 'height', $widgetOptions['textImage']['height']);
        }
        if (isset($widgetOptions['textImage']['bigWidth'])) {
            $parametersMod->setValue('standard', 'content_management', 'widget_text_image', 'big_width', $widgetOptions['textImage']['bigWidth']);
        }
        if (isset($widgetOptions['textImage']['bigHeight'])) {
            $parametersMod->setValue('standard', 'content_management', 'widget_text_image', 'big_height', $widgetOptions['textImage']['bigHeight']);
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
        $themeJsonFile = BASE_DIR . THEME_DIR . $name . '/' . self::INSTALL_DIR . 'Theme.json';
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