<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\System;


class Event
{
    public static function ipUrlChanged($info)
    {
        \Ip\Internal\DbSystem::replaceUrls($info['oldUrl'], $info['newUrl']);
    }

    public static function ipCronExecute($info)
    {
        if ($info['firstTimeThisDay'] || $info['test']) {
            if (ipGetOption('Config.keepOldRevision') != 0) {
                \Ip\Internal\Revision::removeOldRevisions(ipGetOption('Config.keepOldRevision'));
            }
            static::checkForUpdates();
        }
    }

    private static function checkForUpdates()
    {
        $module = new Module();
        $systemInfo = $module->getSystemInfo();
        if ($systemInfo != '') { //send an email
            $md5 = \Ip\ServiceLocator::storage()->get('Ip', 'lastSystemMessageSent');
            if (!$md5 || $md5 != md5($systemInfo)) { //we have a new message
                $message = '';
                $messages = json_decode($systemInfo);
                if (is_array($messages)) {
                    foreach ($messages as $messageKey => $messageVal) {
                        $message .= '<p>' . $messageVal->message . '</p>';
                    }

                    $onlyStatusMessages = true;
                    foreach ($messages as $messageKey => $messageVal) {
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

                ipDispatcher()->notify('ipSystemMessages', array('messages' => $messages));

                \Ip\ServiceLocator::storage()->set('Ip', 'lastSystemMessageSent', md5($systemInfo));
            }


        }
    }

}


