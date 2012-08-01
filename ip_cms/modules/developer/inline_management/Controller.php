<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\developer\inline_management;
if (!defined('CMS')) exit;



class Controller extends \Ip\Controller{


    public function allowAction($action) {
        if (\Ip\Backend::loggedIn()) {
            return \Ip\Backend::userHasPermission(\Ip\Backend::userId(), 'standard', 'content_management');
        } else {
            return false;
        }
    }

    public function getManagementPopup() {
        $html = \Ip\View::create('view/popup/logo.php', array())->render();

        $data = array(
            "status" => "success",
            "html" => $html
        );
        $this->returnJson($data);
    }







}
