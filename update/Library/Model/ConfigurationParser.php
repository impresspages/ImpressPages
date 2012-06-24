<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Library\Model;

class ConfigurationParser
{
    public function parse($installationDir)
    {
        $uniquePrefix = $this->getUniqueInstancePrefix();
        $oldConstants = $this->getAllConstants();
        foreach($oldConstants as $constant) {
            $newConstants[] = $uniquePrefix.'_'.$constant;
        }
         
        
        if(is_file($installationDir.'/ip_config.php')) {
            $configSource = file_get_contents($installationDir.'/ip_config.php');
        } else {
            if (is_file($installationDir.'/../ip_config.php')) {
                $configSource = file_get_contents($installationDir.'/../ip_config.php');
            } else {
                throw new Exception("Can't find configuration file. Installation dir: ".$installationDir);
            }
        }
        
        //rename all constnats to avoid conflicts
        foreach($oldConstants as $key => $constant) {
            $configSource = str_replace('"'.$constant.'"', '"'.$newConstants[$key].'"', $configSource);
            $configSource = str_replace("'".$constant."'", "'".$newConstants[$key]."'", $configSource);
        }
        $configSource = str_replace('<?php', '', $configSource);
        $configSource = preg_replace('/exit\\s*;/i', 'TRUE;', $configSource);
        $configSource = preg_replace('/mb_internal_encoding\\s*\(\\s*CHARSET\\s*\)\\s*;/', '', $configSource);
        
        eval($configSource);

        $configurationValues = array();
        foreach($newConstants as $key => $constant) {
            eval('$configurationValues[\''.$oldConstants[$key].'\'] = '.$constant.';' );
        }
        return $configurationValues;
    }
    
    
    private function getUniqueInstancePrefix() 
    {
        if (isset($_SESSION['ipAutoUpdate']['instancePrefix'])) {
            $_SESSION['ipAutoUpdate']['instancePrefix']++;
        } else {
            $_SESSION['ipAutoUpdate']['instancePrefix'] = 1;
        }
        return 'updateUniquePrefix'.$_SESSION['ipAutoUpdate']['instancePrefix'];
    }    
    
    private function getAllConstants() 
    {
        $constants = array (
            'SESSION_NAME',
            'DB_SERVER',
            'DB_USERNAME',
            'DB_PASSWORD',
            'DB_DATABASE',
            'DB_PREF',
            'BASE_DIR',
            'BASE_URL',
            'IMAGE_DIR',
            'TMP_IMAGE_DIR',
            'IMAGE_REPOSITORY_DIR',
            'FILE_DIR',
            'TMP_FILE_DIR',
            'FILE_REPOSITORY_DIR',
            'VIDEO_DIR',
            'TMP_VIDEO_DIR',
            'VIDEO_REPOSITORY_DIR',
            'AUDIO_DIR',
            'TMP_AUDIO_DIR',
            'AUDIO_REPOSITORY_DIR',
            'DEVELOPMENT_ENVIRONMENT',
            'ERRORS_SHOW',
            'ERRORS_SEND',
            'INCLUDE_DIR',
            'BACKEND_DIR',
            'FRONTEND_DIR',
            'LIBRARY_DIR',
            'MODULE_DIR',
            'CONFIG_DIR',
            'PLUGIN_DIR',
            'THEME_DIR',
            'BACKEND_MAIN_FILE',
            'BACKEND_WORKER_FILE',
            'CHARSET',
            'MYSQL_CHARSET',
            'THEME',
            'DEFAULT_DOCTYPE'
        );    
        return $constants;
    }
}