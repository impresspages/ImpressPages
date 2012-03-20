<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2012 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\administrator\theme;

if (!defined('CMS')) exit;

require_once(__DIR__.'/theme.php');


class Model{

    public static function getAvailableThemes() {
        $answer = array();
        if ($handle = opendir(BASE_DIR.THEME_DIR)) {
            while (false !== ($file = readdir($handle))) {
                if($file != '..' && $file != '.') {
                    $answer[] = new Theme($file);
                }
            }
            closedir($handle);
        }
        

        return $answer;
    }
    
    
    public static function installTheme($themeName) {
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
        
        self::writeThemeNameToConfig(BASE_DIR.'ip_config.php', $theme->getName());
    }
    
    private static function writeThemeNameToConfig($configFileName, $themeName){
        $config = file_get_contents($configFileName);

        $count;
        $config = preg_replace('/[\'\"]THEME[\'\"][ \n]*,[ \n]*[\'\"]'.THEME.'[\'\"]/s', "'THEME', '".$themeName."'", $config, 1, $count);

        if ($count != 1) {
            throw new \Exception('Can\'t find theme definition in configuration file');
        }
        
        file_put_contents($configFileName, $config);
    }
}