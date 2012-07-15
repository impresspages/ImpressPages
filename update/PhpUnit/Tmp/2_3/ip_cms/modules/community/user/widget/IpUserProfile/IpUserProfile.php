<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\community\user\widget;

if (!defined('CMS')) exit;



class IpUserProfile extends \Modules\standard\content_management\Widget{


    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('community', 'user', 'admin_translations', 'registration');
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
        
        $profileForm = \Modules\community\user\Config::getProfileForm();
        
        $data = array (
            'profileForm' => $profileForm,
            'loggedIn' => $session->loggedIn()
        );
        return parent::previewHtml($instanceId, $data, $layout);
        
    }
    
    /**
    * Return true if you like to hide widget in administration panel.
    * You will be able to access widget in your code.
    */
    public function getUnderTheHood() {
        return true;
    }
    
    
}