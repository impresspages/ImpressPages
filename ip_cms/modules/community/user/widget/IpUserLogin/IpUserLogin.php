<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\community\user\widget;

if (!defined('CMS')) exit;



class IpUserLogin extends \Modules\standard\content_management\Widget{


    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('community', 'user', 'admin_translations', 'login');
    }
    
    public function previewHtml($instanceId, $data, $layout) {
        global $session;
        global $site;
        global $parametersMod;
        $userZone = $site->getZoneByModule('community', 'user');
        if (!$userZone) {
            return '
            Please create new zone in Developer / zones with associated module group <b>community</b> and module <b>user</b>.
            ';
        }
        
        if($session->loggedIn()) {
            //TODO
            //if($parametersMod->getValue('community', 'user', 'options', 'zone_after_login'))
            //$answer .= '<script type="text/javascript">document.location = \''.$site->generateUrl(null, $parametersMod->getValue('community', 'user', 'options', 'zone_after_login')).'\';</script>';
            //else
            //$answer .= '<script type="text/javascript">document.location = \''.$site->generateUrl(null, $this->zoneName, array('profile')).'\';</script>';
        }else {
            $loginForm = \Modules\community\user\Config::getLoginForm();
            
            $data = array (
                'loginForm' => $loginForm
            );
            
            return parent::previewHtml($instanceId, $data, $layout);
        }
        
    }    
}