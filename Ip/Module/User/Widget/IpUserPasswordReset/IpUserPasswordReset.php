<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\User\Widget;




class IpUserPasswordReset extends \Ip\Module\Content\Widget{


    
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
        
        $passwordResetForm = \Ip\Module\User\Config::getPasswordResetForm();
        
        $data = array (
            'passwordResetForm' => $passwordResetForm,
            'loggedIn' => $session->loggedIn(),
            'passwordResetEnabled' => $parametersMod->getValue('community', 'user', 'options', 'allow_password_reset')
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