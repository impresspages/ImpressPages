<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\System;


class System{

    public function __construct() {
    }

    public function init(){
        ipDispatcher()->addEventListener('site.urlChanged', __NAMESPACE__ .'\System::urlChanged');
        ipDispatcher()->addEventListener('Cron.execute', array($this, 'executeCron'));
    }
    
    public static function urlChanged($info)
    {
        \Ip\Internal\DbSystem::replaceUrls($info['oldUrl'], $info['newUrl']);
    }
    
    public function executeCron($info)
    {
        if ($info['firstTimeThisDay'] || $info['test']) {
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
            $md5 = \Ip\ServiceLocator::storage()->get('Ip', 'lastSystemMessageSent');
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

                if (ipConfig()->getRaw('ERRORS_SEND')) {
                    $queue = new \Ip\Internal\Email\Module();
                    $queue->addEmail(
                        ipGetOption('Config.websiteEmail'),
                        ipGetOption('Config.websiteTitle'),
                        ipConfig()->getRaw('ERRORS_SEND'),
                        '',
                        ipGetOption('Config.websiteTitle'),
                        $message,
                        false,
                        true);
                    $queue->send();
                }

                \Ip\ServiceLocator::storage()->set('Ip', 'lastSystemMessageSent', md5($systemInfo));
            }



        }
    }

}


