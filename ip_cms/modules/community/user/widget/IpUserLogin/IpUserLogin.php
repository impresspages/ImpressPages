<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\community\user\widget;

if (!defined('CMS')) exit;



class IpUserLogin extends \Ip\Module\Content\Widget{


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
            $data = array ();
            
            $data['logoutUrl'] = $userZone->getLinkLogout(); 

            return parent::previewHtml($instanceId, $data, $layout);
        }else {
            $loginForm = \Modules\community\user\Config::getLoginForm();
            
            $data = array ();
            
            $data['loginForm'] = $loginForm;
            
            if($parametersMod->getValue('community','user','options','allow_password_reset')) {
                $data['passwordResetUrl'] = $userZone->getLinkPasswordReset();
            }

            if($parametersMod->getValue('community','user','options','registration_on_login_page') && $parametersMod->getValue('community','user','options','enable_registration')) {
                $data['registrationUrl'] = $userZone->getLinkRegistration();
            }
            
            
            return parent::previewHtml($instanceId, $data, $layout);
        }
        
    }   

    /**
    * Return true if you like to hide widget in administration panel.
    * You will be able to access widget in your code.
    */
    public function getUnderTheHood() {
        global $site;
        $userZone = $site->getZoneByModule('community', 'user');
        if ($userZone) {
            return false;
        } else {
            return true;
        }
    }    
}