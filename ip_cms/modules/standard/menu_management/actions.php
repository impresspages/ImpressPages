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
                if (!$this->_adminAccess()) {
                    return;
                }

                $languages = Db::getLanguages();
                $data = array (
                    'status' => 'success',
                    'response' => $languages
                );
                $this->_printJson($data);
                break;
            case 'getZones':
                if (!$this->_adminAccess()) {
                    return;
                }
                $zones = Db::getZones();

                if (!isset($_REQUEST['parentId'])) {
                    trigger_error('Parent ID is not set');
                    return;
                }
                $parentId = $_REQUEST['parentId'];

                foreach ($zones as $zoneKey => &$zone) {
                    $zoneElement = Db::rootContentElement($zone['id'], $parentId);


                    if($zoneElement == null) { /*try to create*/
                        Db::createRootZoneElement($zone['id'], $parentId);
                        $zoneElement = Db::rootContentElement($zone['id'], $parentId);
                        if($zoneElement == null) {	/*fail to create*/
                            trigger_error("Can't create root zone element.");
                            return false;
                        }
                    }
                    $zone['elementId'] = $zoneElement;
                }

                $data = array (
                    'status' => 'success',
                    'response' => $zones
                );
                $this->_printJson($data);
                break;
            case 'getChildren':
                if (!$this->_adminAccess()) {
                    return;
                }

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
                if (!$this->_adminAccess()) {
                    return;
                }
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

            switch ($widget['group_key'].'/'.$widget['module_key']) {
                case 'text_photos/photo':
                    $widget['data']['photo'] = str_replace(BASE_DIR, BASE_URL, $widget['data']['photo']);
                    $widget['data']['photo_big'] = str_replace(BASE_DIR, BASE_URL, $widget['data']['photo_big']);
                    break;
                case 'text_photos/text_photo':
                    $widget['data']['photo'] = str_replace(BASE_DIR, BASE_URL, $widget['data']['photo']);
                    $widget['data']['photo_big'] = str_replace(BASE_DIR, BASE_URL, $widget['data']['photo_big']);
                    break;
                case 'misc/file':
                    $widget['data']['photo'] = str_replace(BASE_DIR, BASE_URL, $widget['data']['photo']);
                    break;
                case 'misc/video':
                    $widget['data']['photo'] = str_replace(BASE_DIR, BASE_URL, $widget['data']['photo']);
                    break;
                case 'text_photos/logo_gallery':
                    foreach($widget['data']['logos'] as $logoKey => $logo){
                        $tmpValues = array();
                        $logo['logo'] = str_replace(BASE_DIR, BASE_URL, $logo['logo']);
                    }
                    break;
                case 'text_photos/photo_gallery':
                    foreach($widget['data']['photos'] as $photoKey => $photo){
                        $tmpValues = array();
                        $photo['photo'] = str_replace(BASE_DIR, BASE_URL, $photo['photo']);
                        $photo['photo_big'] = str_replace(BASE_DIR, BASE_URL, $photo['photo_big']);
                    }
                    break;
            }


        }
        $page['widgets'] = $widgets;

        $page['subpages'] = array();
        $subpages = Db::pageChildren($pageId);
        foreach ($subpages as $key => $subpage) {
            $page['subpages'][] = $this->_getPageDataRecursion($subpage['id']);
        }

        return $page;
    }

    private function _adminAccess () {
        require (BASE_DIR.BACKEND_DIR.'db.php');
        if (!isset($_REQUEST['username'])) {
            return false;
        }
        if (!isset($_REQUEST['password'])) {
            return false;
        }

        //check log in
        if(isset($_REQUEST['username']) && isset($_REQUEST['password'])) {

            if(\Backend\Db::incorrectLoginCount($_REQUEST['username'].'('.$_SERVER['REMOTE_ADDR'].')') > 2) {
                \Backend\Db::log('system', 'backend login suspended (menu management)', $_REQUEST['username'].'('.$_SERVER['REMOTE_ADDR'].')', 2);
                return false;
            } else {
                $id = \Backend\Db::userId($_REQUEST['username'], $_REQUEST['password']);
                if($id !== false) {
                    $module = \Db::getModule(null, $groupName = 'standard', $moduleName = 'menu_management');
                    if (\Backend\Db::allowedModule($moduleId = $module['id'], $userId = $id)) {
                        \Backend\Db::log('system', 'backend login (menu management)', $_REQUEST['username'].' ('.$_SERVER['REMOTE_ADDR'].')', 0);
                        return true;
                    } else {
                        \Backend\Db::log('system', 'this user is not allowed to access menu management module', $_REQUEST['username'].'('.$_SERVER['REMOTE_ADDR'].')', 1);
                        return false;
                    }
                } else {
                    \Backend\Db::log('system', 'backend login incorrect (menu management)', $_REQUEST['username'].'('.$_SERVER['REMOTE_ADDR'].')', 1);
                    return false;
                }
            }
        }
        //check log in
        return false;
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
