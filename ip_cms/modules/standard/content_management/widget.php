<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Modules\standard\content_management;

require_once(__DIR__.'/model.php');

class Widget{
    var $name;
    var $moduleGroup;
    var $moduleName;
    
    /**
     * @var boolean - true if widget is installed by default
     */
    var $core;
    const PREVIEW_DIR = 'preview';
    const MANAGEMENT_DIR = 'management';
    const PUBLIC_DIR = 'public';

    public function __construct($name, $moduleGroup, $moduleName, $core = false) {
        $this->name = $name;
        $this->moduleGroup = $moduleGroup;
        $this->moduleName = $moduleName;
        $this->core = $core;

        if ($core) {
            $this->widgetDir = MODULE_DIR.$this->moduleGroup.'/'.$this->moduleName.'/'.Model::WIDGET_DIR.'/'.$this->name.'/';
        } else {
            $this->widgetDir = PLUGIN_DIR.$this->moduleGroup.'/'.$this->moduleName.'/'.Model::WIDGET_DIR.'/'.$this->name.'/';
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
        if (file_exists(BASE_DIR.$this->widgetDir.self::PUBLIC_DIR.'/icon.png')) {
            return $this->widgetDir.self::PUBLIC_DIR.'/icon.png';
        } else {
            return MODULE_DIR.'standard/content_management/img/icon_widget.png';
        }
    }

    public function getLayouts() {
        global $parametersMod;

        $views = array();

        try {

            //collect default view files
            $layoutsDir = BASE_DIR.$this->widgetDir.self::PREVIEW_DIR;
            if (!file_exists($layoutsDir) || !is_dir($layoutsDir)) {
                throw new Exception('Layouts directory does not exist', Exception::NO_LAYOUTS);
            }

            $availableViewFiles = scandir(BASE_DIR.$this->widgetDir.self::PREVIEW_DIR);
            foreach ($availableViewFiles as $viewKey => $viewFile) {
                //$layout = substr($viewFile, 0, -4);
                if (is_file(BASE_DIR.$this->widgetDir.self::PREVIEW_DIR.'/'.$viewFile) && substr($viewFile, -4) == '.php') {
                    $views[substr($viewFile, 0, -4)] = 1;
                }
            }

            //collect overriden theme view files
            $themeViewsFolder = BASE_DIR.THEME_DIR.THEME.'/modules/'.$this->moduleGroup.'/'.$this->moduleName.'/'.Model::WIDGET_DIR.'/'.$this->name.'/'.self::PREVIEW_DIR;
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
                if ($parametersMod->exist($this->moduleGroup, $this->moduleName, 'admin_translations', 'layout_'.$viewKey)) {
                    $translation = $parametersMod->getValue($this->moduleGroup, $this->moduleName, 'admin_translations', 'layout_'.$viewKey);
                } else {
                    if ($viewKey == 'default') {
                        $translation = $parametersMod->getValue('standard', 'content_management', 'admin_translations', 'layout_default');
                    } else {
                        $translation = $viewKey;
                    }
                }
                $layouts[] = array('name' => $viewKey, 'title' => $translation);
            }

            if (empty($layouts)) {
                throw new Exception('No layouts', Exception::NO_LAYOUTS);
            }

        } catch (Exception $e) {
            $layouts[] = array('name' => 'default', 'title' => $parametersMod->getValue('standard', 'content_management', 'admin_translations', 'layout_default'));
        }


        return $layouts;
    }

    /**
     * Return true if you like to hide widget in administration panel.
     * You will be able to access widget in your code.
     */
    public function getUnderTheHood() {
        return false; //by default all widgets are visible; 
    }

    
    
    /**
     *
     *
     * @param $widgetId
     * @param $postData
     * @param $currentData
     * @return array data to be stored to the database
     */
    public function update ($widgetId, $postData, $currentData) {
        return $postData;
    }

    /**
     * 
     * You can make posts directly to your widget. If you will pass following parameters:
     * g=standard
     * m=content_management
     * a=widgetPost
     * instanceId=actualWidgetInstanceId
     * 
     * then that post request will be redirected to this method.
     * 
     * Use $controller->returnJson($data) to return json including correct headers and halt page parsing.
     * 
     * Be carefull. This method is accessible from outside administration panel.
     * If your post should be handled only in administration mode, you need to check that using \Ip\Backend::loggedIn() method
     * Also you prabably would like to check if user has permission to access content_management module: \Ip\Backend::userHasPermission(\Ip\Backend::userId(), 'standard', 'content_management')
     * 
     * @param \Ip\Controller $controller
     * @param int $instanceId
     * @param array $postData untouched post data
     * @param array $data widget data
     */
    public function post ($controller, $instanceId, $postData, $data) {

    }

    /**
     * 
     * Duplicate widget action. This function is executed after the widget is being duplicated.
     * All widget data is duplicated automatically. This method is used only in case a widget
     * needs to do some maintenance tasks on duplication.
     * @param int $oldId old widget id
     * @param int $newId duplicated widget id
     * @param array $data data that has been duplicated from old widget to the new one
     */
    public function duplicate($oldId, $newId, $data) {

    }

    /**
     * 
     * Delete widget. This method is executed before actuall deletion of widget.
     * It is used to remove widget data (photos, files, additional database records and so on).
     * Standard widget data is being deleted automatically. So you don't need to extend this method
     * if your widget does not upload files or add new records to the database manually.
     * @param int $widgetId
     * @param array $data data that is being stored in the widget
     */
    public function delete($widgetId, $data){

    }

    public function managementHtml($instanceId, $data, $layout) {
        $answer = '';
        try {
            $answer = \Ip\View::create(BASE_DIR.PLUGIN_DIR.$this->moduleGroup.'/'.$this->moduleName.'/'.Model::WIDGET_DIR.'/'.$this->name.'/'.self::MANAGEMENT_DIR.'/default.php', $data)->render();
        } catch (\Ip\CoreException $e){
            //do nothing. Administration view does not exist
        }
        return $answer;
    }

    public function previewHtml($instanceId, $data, $layout) {
        $answer = '';
        try {
            $answer = \Ip\View::create(BASE_DIR.PLUGIN_DIR.$this->moduleGroup.'/'.$this->moduleName.'/'.Model::WIDGET_DIR.'/'.$this->name.'/'.self::PREVIEW_DIR.'/'.$layout.'.php', $data)->render();
        } catch (\Ip\CoreException $e){
            global $site;
            
            if ($site->managementState()) {
                $tmpData = array(
                    'widgetName' => $this->name,
                    'layout' => $layout
                );
                $answer = \Ip\View::create('view/unknown_widget_layout.php', $tmpData)->render();
            } else {
                $answer = '';
            }
        }
        return $answer;
    }

    public function dataForJs($data) {
        return $data;
    }
    
    /**
     * This method is called when widget options has been changed.
     * Do any maintenance job needed.
     * Eg. if widget has cropped images, they need to be cropped once again, because cropping options
     * might be changed.
     */
    public function recreate($widgetId, $data) {
        return $data;
    }
}