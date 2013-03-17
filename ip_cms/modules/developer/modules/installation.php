<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\developer\modules;

if (!defined('BACKEND')) exit;

require_once (__DIR__.'/db.php');
require_once (BASE_DIR.MODULE_DIR.'developer/config_exp_imp/parameters.php');
require_once (BASE_DIR.MODULE_DIR.'standard/languages/db.php');
require_once (__DIR__.'/configuration_file.php');
require_once (BASE_DIR.INCLUDE_DIR.'db_system.php');


class ModulesInstallation{

    public function getErrors($moduleGroupKey, $moduleKey, $requiredVersion = null){
        global $parametersMod;
        $errors = array();

        if(file_exists(BASE_DIR.PLUGIN_DIR.$moduleGroupKey.'/'.$moduleKey.'/install/plugin.ini')){
            $configuration = new ConfigurationFile(BASE_DIR.PLUGIN_DIR.$moduleGroupKey.'/'.$moduleKey.'/install/plugin.ini');
            if($configuration->getError()){
                $errors[] = $parametersMod->getValue('developer','modules','admin_translations_install','error_incorrect_ini_file').PLUGIN_DIR.$configuration->getModuleGroupKey()."/".$configuration->getModuleKey().'/install/plugin.ini';
            } else {
                if($requiredVersion && (double)$configuration->getModuleVersion() < (double)$requiredVersion){
                    $errors[] = $parametersMod->getValue('developer','modules','admin_translations_install','error_update_required').$moduleGroupKey.'/'.$moduleKey.' '.$requiredVersion;
                }

                if($moduleGroupKey != $configuration->getModuleGroupKey() || $moduleKey != $configuration->getModuleKey()){
                    $errors[] = $parametersMod->getValue('developer','modules','admin_translations_install','error_move_module').PLUGIN_DIR.$configuration->getModuleGroupKey()."/".$configuration->getModuleKey();
                }


                foreach($configuration->getRequiredModules() as $module){
                    $errors = array_merge($errors, $this->getErrors($module['module_group_key'], $module['module_key'], $module['version']));
                }
            }
        } else {
            if(is_dir(BASE_DIR.PLUGIN_DIR.$moduleGroupKey.'/'.$moduleKey)){
                $errors[] = $parametersMod->getValue('developer','modules','admin_translations_install','error_ini_file_doesnt_exist').PLUGIN_DIR.$moduleGroupKey.'/'.$moduleKey.'/install/plugin.ini';
            } else {
                $errors[] = $parametersMod->getValue('developer','modules','admin_translations_install','error_required_module').' '.$moduleGroupKey.'/'.$moduleKey.': '.$requiredVersion;
            }
        }


        //check ImpressPages server for incompatabilities
        if (count($errors) == 0) {
            $tmpError = $this->_checkCompatability($moduleGroupKey, $moduleKey, $requiredVersion);

            if ($tmpError) {
                $errors[] = $tmpError;
            }
        }

        return $errors;
    }


    private function _checkCompatability($moduleGroup, $moduleName, $version) {
        $answer = '';
        if(function_exists('curl_init')){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://service.impresspages.org');
            curl_setopt($ch, CURLOPT_POST, 1);

            $postFields = 'module_name=communication&module_group=service&action=checkCompatability&newModuleGroup='.$moduleGroup.'&newModuleName='.$moduleName.'&newModuleVersion='.$version.'&version=1';
            $postFields .= '&systemVersion='.\DbSystem::getSystemVariable('version');

            $groups = \Modules\developer\modules\Db::getGroups();
            foreach($groups as $groupKey => $group){
                $modules = \Modules\developer\modules\Db::getModules($group['id']);
                foreach($modules as $moduleKey => $module){
                    $postFields .= '&modules['.$group['name'].']['.$module['name'].']='.$module['version'];
                }
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_REFERER, BASE_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            $json = curl_exec($ch);

            if(json_decode($json) !== null) { //json decode succeded
                $array = json_decode($json, true);
                if ($array['status'] != 'success') {
                    $answer = $array['errorMessage'];
                }
            }
        }
        
        return $answer;
    }


    private function install($moduleGroupKey, $moduleKey){
        $config = new ConfigurationFile(BASE_DIR.PLUGIN_DIR.$moduleGroupKey.'/'.$moduleKey.'/install/plugin.ini');



        $group = Db::getModuleGroup($moduleGroupKey);
        if($group === false){
            Db::insertModuleGroup($config->getModuleGroupTitle(), $config->getModuleGroupKey(), $config->getModuleGroupAdmin());
            $group = Db::getModuleGroup($config->getModuleGroupKey());
        }
        if($group !== false){//group exist or is successfully created
            $newModuleId = Db::insertModule($config->getModuleTitle(), $config->getModuleKey(), $config->getModuleAdmin(), $config->getModuleManaged(), $group['id'], $config->getModuleVersion());
            $module = \Db::getModule($newModuleId);
             
            if($module !== false){
                ModulesArea::after_insert($module['id']);
            }

            $this->importConfig($moduleGroupKey, $moduleKey);

            if(file_exists(BASE_DIR.PLUGIN_DIR.$moduleGroupKey.'/'.$moduleKey.'/install/script.php')){
                require_once(BASE_DIR.PLUGIN_DIR.$moduleGroupKey.'/'.$moduleKey.'/install/script.php');
                eval('$installObject = new \\Modules\\'.$module['g_name'].'\\'.$module['m_name'].'\\Install();');
                $installObject->execute();
            }

        }


    }

    private function update($moduleGroupKey, $moduleKey, $currentVersion){
        $config = new ConfigurationFile(BASE_DIR.PLUGIN_DIR.$moduleGroupKey.'/'.$moduleKey.'/install/plugin.ini');

        if(file_exists(BASE_DIR.PLUGIN_DIR.$moduleGroupKey.'/'.$moduleKey.'/update/script.php')){
            require_once(BASE_DIR.PLUGIN_DIR.$moduleGroupKey.'/'.$moduleKey.'/update/script.php');
            eval('$updateObject = new \\Modules\\'.$moduleGroupKey.'\\'.$moduleKey.'\\Update('.$currentVersion.');');
            $updateObject->execute();
        }
        Db::updateModuleVersion($moduleGroupKey, $moduleKey, $config->getModuleVersion());
    }

    private function importConfig($groupName, $moduleName){
        require_once(BASE_DIR.MODULE_DIR.'developer/localization/manager.php');
        global $log;
        global $parametersMod;
        $siteLanguages = \Modules\standard\languages\Db::getLanguages();

        $installDir = BASE_DIR.PLUGIN_DIR.$groupName.'/'.$moduleName.'/install/';


        //install default config
        if(is_file($installDir.'config.php')){
            \Modules\developer\localization\Manager::saveParameters($installDir.'config.php', true);
        }


        //get public area translation files
        $configFiles = $this->getFiles($installDir, 'translations.public.');

        foreach($siteLanguages as $key => $language){
            $siteLanguages[$key]['code'] = strtolower($siteLanguages[$key]['code']);
        }


        //install configuration files that match site languages
        foreach($siteLanguages as $languageKey => $language){
            if(isset($configFiles[$language['code']])){
                \Modules\developer\localization\Manager::saveParameters($installDir.$configFiles[$language['code']]);
                $siteLanguages[$languageKey] == null; //mark language as installed
            }
        }

        //install configuration files that are similar to site languages. Eg. en-gb and en
        foreach($configFiles as $configKey => $file){
            if($configKey !== 'default'){ //default is already installed
                $fileCode = $configKey;
                $fileCode = substr($fileCode, 0, strpos($fileCode, '-'));
                foreach($siteLanguages as $languageKey => $language){
                    if($language !== null){ //null - already installed
                        $languageCode = substr($language['code'], 0, strpos($language['code'], '-'));
                        if($languageCode = $fileCode){
                            \Modules\developer\localization\Manager::saveParameters($installDir.$configFiles[$configKey]);
                            $siteLanguages[$languageKey] == null; //mark language as installed
                        }
                    }
                }
            }
        }


        //get administration area translation files
        $configFiles = $this->getFiles($installDir, 'translations.administrator.');
        $administrationAreaLanguageCode = $parametersMod->getValue('standard', 'configuration', 'advanced_options', 'administrator_interface_language');
        if(isset($configFiles[$administrationAreaLanguageCode])){
            \Modules\developer\localization\Manager::saveParameters($installDir.$configFiles[$administrationAreaLanguageCode], true);
        } else {
            if(strpos($administrationAreaLanguageCode, '-') !== false){
                $administrationAreaLanguageCode = substr($administrationAreaLanguageCode, 0, strpos($administrationAreaLanguageCode, '-'));
            }
            foreach($configFiles as $configKey => $file){
                $fileCode = $configKey;
                $fileCode = substr($fileCode, 0, strpos($fileCode, '-'));
                if($fileCode == $administrationAreaLanguageCode){
                    \Modules\developer\localization\Manager::saveParameters($installDir.$configFiles[$configKey], true);
                }
            }
        }


    }


    private function getFiles($dir, $prefix){
        $answer = array();
        if(file_exists($dir) && is_dir($dir)){
            $handle = opendir($dir);
            if($handle !== false){
                while (false !== ($file = readdir($handle))) {
                    if(is_file($dir.$file) && strpos($file, $prefix) === 0){
                        $languageCode = substr($file, strlen($prefix));
                        $languageCode = substr($languageCode, 0, strpos($languageCode, '.'));
                        $answer[$languageCode] = $file;
                    }
                }
            }
        }
        return $answer;
    }























    public function recursiveInstall($moduleGroupKey, $moduleKey){
        //we don't need to check any errors. They are found before.
        $module = \Db::getModule(null, $moduleGroupKey, $moduleKey);
        $configuration = new ConfigurationFile(BASE_DIR.PLUGIN_DIR.$moduleGroupKey.'/'.$moduleKey.'/install/plugin.ini');

        $dependendModules = $configuration->getRequiredModules();
        foreach($dependendModules as $key => $dependendModule){
            $this->recursiveInstall($dependendModule['module_group_key'], $dependendModule['module_key']);
        }
        if($module){ //if module exists - update
            if((double)$configuration->getModuleVersion() > (double)$module['version']){
                $this->update($moduleGroupKey, $moduleKey, (double)$module['version']);
            }
        } else {
            $this->install($moduleGroupKey,  $moduleKey);
        }
    }

    public function findNewModules(){
        global $cms;
        global $parametersMod;
        $answer = '';
        $newModules = array();

        $newModuleGroups = $this->getFolders(PLUGIN_DIR);

        foreach($newModuleGroups as $key  => $newModuleGroup){
            $newModuleGroups[$key] = self::getFolders(PLUGIN_DIR.$key."/");
        }

        foreach($newModuleGroups as $newModuleGroupKey => $newModuleGroup){
            foreach($newModuleGroup as $newModuleKey => $newModule){
                $currentModule = \Db::getModule(null, $newModuleGroupKey, $newModuleKey);
                if(file_exists(BASE_DIR.PLUGIN_DIR.$newModuleGroupKey.'/'.$newModuleKey.'/install/plugin.ini')){
                    $configuration = new ConfigurationFile(BASE_DIR.PLUGIN_DIR.$newModuleGroupKey.'/'.$newModuleKey.'/install/plugin.ini');
                    if(!$currentModule){
                        $newModules[] = array('action'=>'insert', 'configuration' => $configuration, 'dependend'=> false);
                    } else {
                        if ((double)$currentModule['version'] < (double)$configuration->getModuleVersion()){
                            $newModules[] = array('action'=>'update', 'configuration' => $configuration, 'dependend'=>false);
                        }
                    }
                } else {
                    $answer .= $parametersMod->getValue('developer', 'modules', 'admin_translations_install', 'error_ini_file_doesnt_exist').'<b>'.$newModuleGroupKey.'/'.$newModuleKey.'/install/plugin.ini</b>';
                }
            }
        }


        if(sizeof($newModules) > 0){
            $answer .= '<link media="screen" rel="stylesheet" type="text/css" href="'.BASE_URL.MODULE_DIR.'developer/modules/style.css"/>';
        }
        foreach($newModules as $key2 => &$newModule2){ //fill alreadyUsed array;
            $this->setDepend($newModules, $newModule2, 1);
        }
        foreach($newModules as $key => $newModule){
            if(!$newModule['dependend']){
                $answer .= $this->printInstallBlock($newModules, $newModule, $alreadyUsed2, 1);
            }
        }

        return $answer;
    }






    private function setDepend(&$allModules, &$newModule, $level){
        if($level != 1){
            $newModule['dependend'] = true;
        }

        foreach($newModule['configuration']->getRequiredModules() as $key => $dependendModule){
            foreach($allModules as $key => &$tmpModule){
                if($tmpModule['configuration']->getModuleGroupKey() == $dependendModule['module_group_key'] && $tmpModule['configuration']->getModuleKey() == $dependendModule['module_key']){
                    $this->setDepend($allModules, $tmpModule, $level+1);
                }
            }
        }
    }


    private function printInstallBlock($allModules, $newModule, &$alreadyUsed, $level){
        global $parametersMod;
        global $cms;
        $answer = '';
         
        $tmpHtml = '';
        foreach($newModule['configuration']->getRequiredModules() as $key => $dependendModule){
            foreach($allModules as $tmpModule){
                if($tmpModule['configuration']->getModuleGroupKey() == $dependendModule['module_group_key'] && $tmpModule['configuration']->getModuleKey() == $dependendModule['module_key']){
                    $tmpHtml .= $this->printInstallBlock($allModules, $tmpModule, $alreadyUsed, $level+1);
                }
            }
        }
         
        if($tmpHtml != ''){
            $tmpHtml = '<br /><br />'.$tmpHtml;
        }

        if($newModule['action'] == 'insert'){
            $moduleName = $newModule['configuration']->getModuleGroupKey().'/'.$newModule['configuration']->getModuleKey().' '.$newModule['configuration']->getModuleVersion();
            $answer .= '
        <div class="newModule">      
          <p>'.$parametersMod->getValue('developer', 'modules', 'admin_translations_install', 'new_module_detected').' <b>'.htmlspecialchars($moduleName).'</b></p>
          <a onclick="LibDefault.ajaxMessage(\''.$cms->generateUrl().'\', \'type=ajax&action=install&module_group='.$newModule['configuration']->getModuleGroupKey().'&module='.$newModule['configuration']->getModuleKey().'\')" class="button">'.htmlspecialchars($parametersMod->getValue('developer', 'modules', 'admin_translations_install', 'install')).'</a>
          '.$tmpHtml.'
          <div class="clear"></div>
        </div>
        
        ';
        } else { //update
            $moduleName = $newModule['configuration']->getModuleGroupKey().'/'.$newModule['configuration']->getModuleKey().':'.$newModule['configuration']->getModuleVersion();
            $answer .= '
        <div class="newModule">      
          <p>'.$parametersMod->getValue('developer', 'modules', 'admin_translations_install', 'updated_module_detected').' <b>'.htmlspecialchars($moduleName).'</b></p>
          <a onclick="LibDefault.ajaxMessage(\''.$cms->generateUrl().'\', \'type=ajax&action=install&module_group='.$newModule['configuration']->getModuleGroupKey().'&module='.$newModule['configuration']->getModuleKey().'\')" class="button">'.htmlspecialchars($parametersMod->getValue('developer', 'modules', 'admin_translations_install', 'update')).'</a>
          '.$tmpHtml.'
          <div class="clear"></div>
        </div>
        
        ';
        }
         
        return $answer;
    }



    private function getFolders($dir){
        $answer = array();
        if(file_exists($dir) && is_dir($dir)){
            $handle = opendir($dir);
            if($handle !== false){
                while (false !== ($file = readdir($handle))) {
                    if(is_dir($dir.$file) && $file != '..' && $file != '.' && substr($file, 0, 1) != '.')
                    $answer[$file] = array();
                }
                return $answer;
            }
        }
    }



     

     


}

