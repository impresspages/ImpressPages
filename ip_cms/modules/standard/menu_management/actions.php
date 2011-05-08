<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\standard\menu_management;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

require_once (__DIR__.'/db.php');
require_once (BASE_DIR.MODULE_DIR.'standard/content_management/widgets/widget.php');


class Actions {

    function __construct() {
    }

    function makeActions() {
        global $site;
        global $parametersMod;
        
        if (!isset($_REQUEST['action'])) {
            return;
        }

        switch ($_REQUEST['action']) {
            case 'getLanguages':
                $languages = Db::getLanguages();
                $data = array (
                    'status' => 'success',
                    'response' => $languages
                );                
                $this->_printJson($data);
                break;
            case 'getZones':
                $zones = Db::getZones();
                $data = array (
                    'status' => 'success',
                    'response' => $zones
                );
                $this->_printJson($data);
                break;
            case 'getChildren':

                if (!isset($_REQUEST['parentId'])) {
                    trigger_error('Parent ID is not set');
                    return;   
                }
                $children = Db::pageChildren($_REQUEST['parentId']);                
                $data = array (
                    'status' => 'success',
                    'response' => $children
                );
                $this->_printJson($data);

                break;
            case 'getData':
                if (!isset($_REQUEST['pageId'])) {
                    trigger_error('Page ID is not set');
                    return;   
                }
                $pageId = $_REQUEST['pageId'];
                
                $pages = array($this->_getPageDataRecursion($pageId)); 

                $data = array (
                    'status' => 'success',
                    'response' => $pages
                );
                $this->_printJson($data);
                break;
                
        }
        
        \Db::disconnect();
        exit;
    }


    private function _getPageDataRecursion($pageId) {
        
        $page = Db::getPage($pageId);

        $widgets = Db::pageWidgets($page['id']);
        foreach($widgets as $key => &$widget){
            require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/'.$widget['group_key'].'/'.$widget['module_key'].'/module.php');
            eval ('$widgetObject = new \\Modules\\standard\\content_management\\Widgets\\'.$widget['group_key'].'\\'.$widget['module_key'].'\\Module(); ');
            $widget['data'] = $widgetObject->getData($widget['module_id']);
        }
        $page['widgets'] = $widgets;
        
        $page['subpages'] = array();
        $subpages = Db::pageChildren($pageId);
        foreach ($subpages as $key => $subpage) {
            $page['subpages'][] = $this->_getPageDataRecursion($subpage['id']);
        }

        return $page;
    }

    private function _printJson ($data) {
        header("HTTP/1.0 200 OK");
        header('Content-type: text/json; charset=utf-8');
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Pragma: no-cache");
        echo json_encode($data);

    }


}
