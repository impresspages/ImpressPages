<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\standard\menu_management;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

require_once (__DIR__.'/db.php');


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
                $this->_printJson($languages);
                break;
            case 'getZones':
                $zones = Db::getZones();
                $this->_printJson($zones);
                break;
            case 'getChildren':
                if (!isset($_REQUEST['parentId'])) {
                    trigger_error('Parent ID is not set');
                    return;   
                }
                $children = Db::pageChildren($_REQUEST['parentId']);                
                $this->_printJson($children);
                break;
                
        }
        
        \Db::disconnect();
        exit;
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
