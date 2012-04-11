<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Modules\administrator\theme;

if (!defined('BACKEND')) exit;

class ConfigurationFile{
    private $themeTitle;
    private $themeVersion;
    private $themeDoctype;

    public function __construct($file){
        global $parametersMod;

        $this->requiredModules = array();

        $moduleGroupAdmin = 0;
        $moduleAdmin = 0;

        $config = $this->getInitVariables($file);

        foreach($config as $variable){
            $key = $variable['name'];
            $value = $variable['value'];
            switch($key){
                case 'title':
                    $this->themeTitle = $value;
                    break;
                case 'version':
                    $this->themeVersion = $value;
                    break;
                case 'doctype':
                    $this->themeDoctype = $value;
                    break;
            }
        }
    }


    public function getThemeTitle(){
        return $this->themeTitle;
    }
    public function getThemeVersion(){
        return $this->themeVersion;
    }
    public function getThemeDoctype(){
        return $this->themeDoctype;
    }
    
    private function getInitVariables($file){
        $answer = array();
    
        if(file_exists($file)){
            $config = file($file);
            foreach($config as $key => $configRow){
                $configName = substr($configRow, 0, strpos($configRow, ':'));
                $value = substr($configRow, strpos($configRow, ':') + 1);
                $value = str_replace("\n", "", str_replace("\r", "", $value));
                $answer[] = array('name'=>$configName, 'value' => $value);
            }
        } else {
            return array();
        }
        return $answer;
    }    
}