<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\User;


class System {


    function init(){
        global $site;
        global $dispatcher;

        $site->addJavascript(\Ip\Config::libraryUrl('js/jquery/jquery.js'));
        $site->addJavascript(\Ip\Config::libraryUrl('js/jquery-tools/jquery.tools.form.js'));
        $site->addJavascript(\Ip\Config::oldModuleUrl('community/user/assets/ipUser.js'));
        
        $dispatcher->bind('site.generateBlock', __NAMESPACE__ .'\System::generateContent');
        $dispatcher->bind('site.generateBlock', __NAMESPACE__ .'\System::generateLogin');

        $dispatcher->bind('site.generateSlot', __NAMESPACE__ .'\System::generateLoginSlot');

    }
    
    
    
    public static function generateContent (\Ip\Event $event) {
        global $site;

        $blockName = $event->getValue('blockName');
        if (
            $blockName != 'main' ||
            $site->getCurrentZone()->getAssociatedModule() != 'user' ||
            $site->getCurrentZone()->getAssociatedModuleGroup() != 'community'
        ) {
            return;
        }
        $event->setValue('content', $site->getCurrentElement()->generateContent() );
        $event->addProcessed();
        
    }

    public static function generateLogin (\Ip\Event $event) {
        global $site;
        $blockName = $event->getValue('blockName');
        if ($blockName == 'ipUserLogin') {
            $userZone = $site->getZoneByModule('community', 'user');
            if (!$userZone) {
                if ($site->managementState()) {
                    $event->setValue('content', 'Please create new zone in Developer / zones with associated module group <b>community</b> and module <b>user</b>.' );
                    $event->addProcessed();
                    return;
                } else { 
                    return;
                }
            }
            $loginBox = $userZone->generateLogin();
            $event->setValue('content', $loginBox );
            $event->addProcessed();
        }
    }

    public static function generateLoginSlot (\Ip\Event $event) {
        global $site;
        $name = $event->getValue('slotName');
        if ($name == 'ipUserLogin') {
            $userZone = $site->getZoneByModule('community', 'user');
            if (!$userZone) {
                if ($site->managementState()) {
                    $event->setValue('content', 'Please create new zone in Developer / zones with associated module group <b>community</b> and module <b>user</b>.' );
                    $event->addProcessed();
                    return;
                } else {
                    return;
                }
            }
            $loginBox = $userZone->generateLogin();
            $event->setValue('content', $loginBox );
            $event->addProcessed();
        }
    }
    
    /**
     * Autologin
     */
    public function catchEvent($moduleGroup, $moduleName, $event, $parameters) {
        global $session;
        global $parametersMod;

        if (!isset($session) || $session->loggedIn()) {  //in admin.php $session is not defined on time of this event.
            return;
        }

        if (!$parametersMod->getValue('community','user','options','enable_autologin')) {
            return;
        }
        if ($moduleGroup == 'administrator' && $moduleName == 'system' && $event == 'init') {
            if (isset($_COOKIE[Config::$autologinCookieName])) {
                $jsonData = $_COOKIE[Config::$autologinCookieName];
                $data = json_decode($jsonData);
                if ($data && isset($data->id) && isset($data->pass) ) {
                    $tmpUser = Db::userById($data->id);
                    if ($tmpUser) {
                        if (md5($tmpUser['password'].$tmpUser['created_on']) == $data->pass) {
                            $session->login($tmpUser['id']);
                            setCookie(
                            Config::$autologinCookieName,
                            json_encode(array('id' => $tmpUser['id'], 'pass' => md5($tmpUser['password'].$tmpUser['created_on']))),
                            time() + $parametersMod->getValue('community','user','options','autologin_time') * 60 * 60 * 24,
                            Config::$autologinCookiePath,
                            Config::getCookieDomain()
                            );
                        }
                    }
                }
            }
        }

    }

}