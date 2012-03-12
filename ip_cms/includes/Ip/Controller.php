<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Ip;


if (!defined('CMS')) exit;

/**
 *
 * Event dispatcher class
 *
 */
class Controller{

    public function allowAction($action){
        return true;
    }
    
    /**
     * Do any initializatoin becore actual controller method
     */
    public function init() {
    }
    
    public function returnJson($data) {
        global $site;
        header('Content-type: text/json; charset=utf-8'); //throws save file dialog on firefox if iframe is used
        $answer = json_encode($data);
        $site->setOutput($answer);
    }
    
    public function redirect ($url) {
        global $dispatcher;
        header("location: ".$url);
        \Db::disconnect();
        $dispatcher->notify(new \Ip\Event($site, 'site.databaseDisconnect', null));
        exit;
    }
}