<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\User\Widget;




class IpUserRegistration extends \Ip\Module\Content\Widget{


    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('User.registration');
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
        
        $registrationForm = \Ip\Module\User\Config::getRegistrationForm();
        
        $data = array (
            'registrationForm' => $registrationForm,
            'loggedIn' => $session->loggedIn(),
            'registrationEnabled' => $parametersMod->getValue('User.enable_registration')
        );
        return parent::previewHtml($instanceId, $data, $layout);
        
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