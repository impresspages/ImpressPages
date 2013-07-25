<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\standard\design;


class Model{

    protected function __construct()
    {

    }

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
        if ($handle = opendir(BASE_DIR.THEME_DIR)) {
            while (false !== ($file = readdir($handle))) {
                if(is_dir(BASE_DIR.THEME_DIR.$file) && $file != '..' && $file != '.' && substr($file, 0, 1) != '.') {
                    $answer[] = new Theme($file);
                }
            }
            closedir($handle);
        }
        

        return $answer;
    }
    
    
    public function installTheme($themeName)
    {
        $availableThemes = self::getAvailableThemes();
        $theme = null;
        foreach($availableThemes as $availableTheme) {
            if ($availableTheme->getName() == $themeName) {
                $theme = $availableTheme;
                break;
            }
        }
        
        if (!$theme) {
            throw new \Exception("Theme '".$themeName."' does not exist.");
        }
        
        $configModel = new \Modules\standard\configuration\Model();
        $configModel->changeConfigurationConstantValue('THEME', THEME, $theme->getName());
        $configModel->changeConfigurationConstantValue('DEFAULT_DOCTYPE', DEFAULT_DOCTYPE, $theme->getDoctype());
        
        $parametersFile = BASE_DIR.THEME_DIR.$themeName.'/'.Theme::INSTALL_DIR.'/'.Theme::PARAMETERS_FILE; 
        if (file_exists($parametersFile)) {
            require_once(BASE_DIR.MODULE_DIR.'developer/localization/manager.php');
            \Modules\developer\localization\Manager::saveParameters($parametersFile);
        }
        
        \DbSystem::setSystemVariable('theme_changed', time());
        
    }
    
}