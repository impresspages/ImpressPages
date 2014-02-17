<?php
/**
 * @package ImpressPages

 *
 */

namespace IpUpdate\Library\Model;

class ConfigurationParser
{
    static $instancePrefix;
    public function parse($installationDir)
    {
        $uniquePrefix = $this->getUniqueInstancePrefix();
        $oldConstants = $this->getAllConstants();
        foreach($oldConstants as $constant) {
            $newConstants[] = $uniquePrefix.'_'.$constant;
        }


        if(is_file($installationDir.'/ip_config.php')) {
            return include($installationDir.'/ip_config.php');

            $configSource = file_get_contents($installationDir.'/ip_config.php');
        } else {
            if (is_file($installationDir.'/../ip_config.php')) {
                return include($installationDir.'/../ip_config.php');

                $configSource = file_get_contents($installationDir.'/../ip_config.php');
            } else {
                throw new \IpUpdate\Library\UpdateException("Can't find configuration file. Installation dir: ".$installationDir, \IpUpdate\Library\UpdateException::UNKNOWN);
            }
        }

        //rename all constants to avoid conflicts
        foreach($oldConstants as $key => $constant) {
            $configSource = str_replace('"'.$constant.'"', '"'.$newConstants[$key].'"', $configSource);
            $configSource = str_replace("'".$constant."'", "'".$newConstants[$key]."'", $configSource);
        }
        $configSource = str_replace('<?php', '', $configSource);
        $configSource = preg_replace('/exit\\s*;/i', 'TRUE;', $configSource);

        eval($configSource);

        $configurationValues = array();
        foreach($newConstants as $key => $constant) {
            //we check if constant exists becase early 2.x versions had no constants like: SECURE_DIR, TMP_SECURE_DIR, MANUAL_DIR
            eval('$configurationValues[\''.$oldConstants[$key].'\'] = defined(\''.$constant.'\') ? '.$constant.' : \'\';' );
        }
        return $configurationValues;
    }


    private function getUniqueInstancePrefix()
    {
        if (self::$instancePrefix) {
            self::$instancePrefix++;
        } else {
            self::$instancePrefix = 1;
        }
        return 'updateUniquePrefix'.self::$instancePrefix;
    }

    private function getAllConstants()
    {
        $constants = array (
            'sessionName',
            'dbPrefix',
            'baseDir',
            'baseUrl',
            'FILE_DIR',
            'TMP_FILE_DIR',
            'FILE_REPOSITORY_DIR',
            'SECURE_DIR',
            'TMP_SECURE_DIR',
            'DEVELOPMENT_ENVIRONMENT',
            'ERRORS_SHOW',
            'THEME_DIR',
            'CHARSET',
            'MYSQL_CHARSET',
            'THEME',
            'DEFAULT_DOCTYPE',
            'SECURE_DIR',
            'TMP_SECURE_DIR',
            'MANUAL_DIR',
            'TEST_MODE',
            'MISSING_CONSTANT_USED_FOR_TESTING_TO_CHECK_IF_CODE_WORKS_IF_SOME_CONSTANTS_ARE_MISSING'
        );
        return $constants;
    }
}
