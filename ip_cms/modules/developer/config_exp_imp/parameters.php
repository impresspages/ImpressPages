<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\developer\config_exp_imp;


require_once (__DIR__.'/parameter_group.php');
require_once (__DIR__.'/db.php');
require_once (BASE_DIR.MODULE_DIR.'standard/languages/db.php');

class Parameters{
    public $parameterGroups;
    public $languageId;
    public $languageCode;
    private $types;
    public $cachedLanguage;

    function __construct($languageId, $types){
        $this->parameterGroups = array();
        $this->languageId = (int)$languageId;
        $this->cachedLanguage = Db::getLanguage($languageId);
        $this->languageCode = $this->cachedLanguage['code'];
        $this->types = $types;
    }


    public function loadParameters($moduleId){
        $groups = Db::getParameterGroups($moduleId);
        foreach($groups as $key => $group){
            $tmpParameterGroup = new ParameterGroup($group);
            $tmpParameterGroup->loadString($this->types);
            $tmpParameterGroup->loadInteger($this->types);
            $tmpParameterGroup->loadBool($this->types);
            $tmpParameterGroup->loadLang($this->types, $this->languageId);
            $this->parameterGroups[] = $tmpParameterGroup;
        }
    }

    public function saveParameters($moduleGroup = null, $moduleName = null, $allowCreateLanguage = true){
        global $parametersMod;
        $this->languageId = Db::getLanguageId($this->cachedLanguage['code']);

        if ($this->languageId === false) {
            if ($allowCreateLanguage) {
                $this->languageId = Db::insertLanguage($this->cachedLanguage);
                \Modules\standard\languages\Db::createEmptyTranslations($this->languageId, 'par_lang');
            } else {
                $siteLanguages = \Modules\standard\languages\Db::getLanguages();
                $this->languageId = $siteLanguages[0]['id'];
            }
        }

        foreach($this->parameterGroups as $groupKey => $group){
            if($moduleGroup == null || $moduleName == null || ($moduleGroup == $group->moduleGroupName && $moduleName == $group->moduleName)){
                if($parametersMod->getValue('standard', 'configuration', 'advanced_options', 'administrator_interface_language') == $this->languageCode){
                    //set module group translation
                    $tmpGroup = Db::getGroup($group->moduleGroupName);
                    if($tmpGroup && $group->moduleGroupTranslation) {
                        Db::updateModuleGroupTranslation($tmpGroup['id'], $group->moduleGroupTranslation);
                    }

                    //set module translation
                    $tmpModule = \Db::getModule(null, $group->moduleGroupName, $group->moduleName);
                    if($tmpModule && $group->moduleTranslation) {
                        Db::updateModuleTranslation($tmpModule['id'], $group->moduleTranslation);
                    }

                }
                $group->saveToDb($this->languageId);
            }

        }
    }

    public function previewParameters(){
        $answer = '';

        $previewGroups = array();

        foreach($this->parameterGroups as $groupKey => $group){
            $previewGroups[$group->moduleGroupName][$group->moduleName][] = $group;
        }

        foreach($previewGroups as $moduleGroupKey => $modules){
            foreach($modules as $moduleKey => $module){
                $answer .= '<div class="content">';
                $answer .= '<h1>'.htmlspecialchars($moduleGroupKey.' -> '.$moduleKey).'</h1>';
                foreach($module as $paramtersGroupKey => $parametersGroup)
                $answer .= $parametersGroup->preview();
                $answer .= '</div>';
            }
        }


        return $answer;
    }

}

