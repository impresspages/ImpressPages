<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\System;


class System{

    public function __construct() {
    }

    public function init(){
        if (\Ip\ServiceLocator::getContent()->isManagementState()) {
            ipAddJavascript(ipGetConfig()->coreModuleUrl('System/public/system.js'), 0);
        }

        ipDispatcher()->bind(\Ip\Event\UrlChanged::URL_CHANGED, __NAMESPACE__ .'\System::urlChanged');
        ipDispatcher()->bind('Cron.execute', array($this, 'executeCron'));
    }
    
    public static function urlChanged (\Ip\Event\UrlChanged $event)
    {
        \Ip\Internal\DbSystem::replaceUrls($event->getOldUrl(), $event->getNewUrl());
    }
    
    public function executeCron(\Ip\Event $e)
    {
        if ($e->getValue('firstTimeThisDay') || $e->getValue('test')) {
            if (ipGetOption('Config.keepOldRevision') != 0) {
                \Ip\Revision::removeOldRevisions(ipGetOption('Config.keepOldRevision'));
            }
            $this->checkForUpdates();
        }
    }


    private function checkForUpdates()
    {
        $module = new Module();
        $systemInfo = $module->getSystemInfo();
        if ($systemInfo != '') { //send an email
            $md5 = \Ip\Internal\DbSystem::getSystemVariable('last_system_message_sent');
            if( !$md5 || $md5 != md5($systemInfo) ) { //we have a new message
                $message = '';
                $messages = json_decode($systemInfo);
                if(is_array($messages)) {
                    foreach($messages as $messageKey => $messageVal) {
                        $message .= '<p>'.$messageVal->message.'</p>';
                    }

                    $onlyStatusMessages = true;
                    foreach($messages as $messageKey => $messageVal) {
                        if ($messageVal->type != 'status') {
                            $onlyStatusMessages = false;
                        }
                    }

                    if ($onlyStatusMessages) {
                        return; //TODO replace to something that would not terminate execution of following scripts if they will be there some day
                    }

                } else {
                    return; //TODO replace to something that would not terminate execution of following scripts if they will be there some day
                }

                if (ipGetConfig()->getRaw('ERRORS_SEND')) {
                    $queue = new \Ip\Module\Email\Module();
                    $queue->addEmail(
                        ipGetOption('Config.websiteEmail'),
                        ipGetOption('Config.websiteTitle'),
                        ipGetConfig()->getRaw('ERRORS_SEND'),
                        '',
                        ipGetOption('Config.websiteTitle'),
                        $message,
                        false,
                        true);
                    $queue->send();
                }

                \Ip\Internal\DbSystem::setSystemVariable('last_system_message_sent', md5($systemInfo));
            }



        }
    }

}


