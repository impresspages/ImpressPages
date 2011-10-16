<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;

class Widget{
    var $name;
    var $moduleGroup;
    var $moduleName;
    var $core;
    const PREVIEW_DIR = 'preview';
    const MANAGEMENT_DIR = 'management';
    const PUBLIC_DIR = 'management';
    
    public function __construct($name, $moduleGroup, $moduleName, $core = false) {
        $this->name = $name;
        $this->moduleGroup = $moduleGroup;
        $this->moduleName = $moduleName;
        $this->core = $core;
        
        if ($core) {
            $this->widgetDir = MODULE_DIR.$this->moduleGroup.'/'.$this->moduleName.'/'.IP_DEFAULT_WIDGET_FOLDER.'/'.$this->name.'/';
        } else {
            $this->widgetDir = PLUGIN_DIR.$this->moduleGroup.'/'.$this->moduleName.'/'.IP_DEFAULT_WIDGET_FOLDER.'/'.$this->name.'/';
        }
    }
    
    public function getTitle() {
        return self::getName();
    }
    
    public function getName() {
        return $this->name;    
    }
    
    public function getModuleGroup() {
        return $this->moduleGroup;
    }
    
    public function getModuleName() {
        return $this->moduleName;
    }
    
    public function getCore() {
        return $this->core;    
    }
    
    public function getIcon() {
        if (file_exists(BASE_DIR.$this->widgetDir.'icon.gif')) {
            return $this->widgetDir.'icon.gif';
        } else {
            return MODULE_DIR.'standard/content_management/img/default_icon.gif';
        }
    }
    
    public function getLayouts() {
        global $parametersMod;
        
        $views = array();
        
        try {
            
            //collect default view files
            $layoutsDir = BASE_DIR.$this->widgetDir.self::PREVIEW_DIR;
            if (!file_exists($layoutsDir) || !is_dir($layoutsDir)) {
                throw new Exception('Layouts directory does not exist', self::NO_LAYOUTS);
            }
            
            $availableViewFiles = scandir(BASE_DIR.$this->widgetDir.self::PREVIEW_DIR);
            foreach ($availableViewFiles as $viewKey => $viewFile) {
                //$layout = substr($viewFile, 0, -4);
                if (is_file(BASE_DIR.$this->widgetDir.self::PREVIEW_DIR.'/'.$viewFile) && substr($viewFile, -4) == '.php') {
                    $views[substr($viewFile, 0, -4)] = 1;
                }
            }
    
            //collect overriden theme view files
            $themeViewsFolder = BASE_DIR.THEME_DIR.THEME.'/modules/'.$this->moduleGroup.'/'.$this->moduleName.'/'.IP_DEFAULT_WIDGET_FOLDER.'/'.$this->name.'/'.self::PREVIEW_DIR;
            if (file_exists($themeViewsFolder) && is_dir($themeViewsFolder)){
                $availableViewFiles = scandir($themeViewsFolder);
                foreach ($availableViewFiles as $viewKey => $viewFile) {
                    $layout = substr($viewFile, 0, -4);
                    if (is_file($themeViewsFolder.'/'.$viewFile) && substr($viewFile, -4) == '.php') {
                        $views[substr($viewFile, 0, -4)] = 1;
                    }
                }
            }
            
            $layouts = array();
            foreach ($views as $viewKey => $view) {
                if ($parametersMod->exist($this->moduleGroup, $this->moduleName, 'translations', 'layout_'.$viewKey)) {
                    $translation = $parametersMod->getValue($this->moduleGroup, $this->moduleName, 'translations', 'layout_'.$viewKey);
                } else {
                    $translation = $viewKey;
                }
                $layouts[] = array('name' => $viewKey, 'title' => $translation);
            }
            
            if (empty($layouts)) {
                throw new Exception('No layouts', self::NO_LAYOUTS);
            }
            
        } catch (Exception $e) {
            $layouts[] = array('name' => 'default', 'title' => $parametersMod->getValue('standard', 'content_management', 'admin_translations', 'default'));
        }

        
        return $layouts;
    }
    
    public function post ($instanceId, $postData, $data) {
        
    }
    
    public function duplicate($oldId, $newId) {
            
    }
    
    public function delete($widgetId){

    }
    
    public function managementHtml($instanceId, $data, $layout) {
        $answer = '';
        try {
            $answer = \Ip\View::create(BASE_DIR.PLUGIN_DIR.$this->moduleGroup.'/'.$this->moduleName.'/'.IP_DEFAULT_WIDGET_FOLDER.'/'.$this->name.'/'.self::MANAGEMENT_DIR.'/default.php', $data)->render();
        } catch (\Ip\CoreException $e){
            //do nothing. Administration view does not exist
        }
        return $answer;         
    }
    
    public function previewHtml($instanceId, $data, $layout) {
        $answer = '';
        try {
            $answer = \Ip\View::create(BASE_DIR.PLUGIN_DIR.$this->moduleGroup.'/'.$this->moduleName.'/'.IP_DEFAULT_WIDGET_FOLDER.'/'.$this->name.'/'.self::PREVIEW_DIR.'/'.$layout.'.php', $data)->render();
        } catch (\Ip\CoreException $e){
            $tmpData = array(
                'widgetName' => $this->name,
                'layout' => $layout
            );
            $answer = \Ip\View::create('view/unknown_widget_layout.php', $tmpData)->render();
        }
        return $answer;    
    }
    

}