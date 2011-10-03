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
        $availableViews = scandir(BASE_DIR.$this->widgetDir);
        
        $layouts = array();
        foreach ($availableViews as $viewKey => $viewFile) {
            $layout = substr($viewFile, 0, -4);
            if (is_file($viewFile) && substr($viewFile, -4) == '.php') {
                if ($parametersMod->exist($this->moduleGroup, $this->moduleName, 'translations', 'layout_'.$layout)) {
                    $translation = $parametersMod->getValue($this->moduleGroup, $this->moduleName, 'translations', 'layout_'.$layout);
                } else {
                    $translation = $layout;
                }
                $layouts[] = array('name' => $layout, 'title' => $translation);
            }
        }
        return $layouts;
    }
    
    public function post ($instanceId, $postData, $data) {
        
    }
    
    public function duplicate($oldId, $newId) {
            
    }
    
    public function delete($widgetId){

    }
    
    public function managementHtml($widgetId, $data, $layout) {
        return '<p>Add "management/default.php" view file to replace this content.</p>';
    }
    
    public function previewHtml($widgetId, $data, $layout) {
        
    }
    

}