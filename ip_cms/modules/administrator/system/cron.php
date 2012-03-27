<?php

/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\administrator\system;

if (!defined('CMS'))
exit;


require_once(__DIR__ . "/module.php");

class Cron {

    function execute($options) {
        global $parametersMod;
        global $dbSite;
        global $log;
        global $site;
        
        
        if ($options->firstTimeThisDay) {
            if ($parametersMod->getValue('standard', 'configuration', 'advanced_options', 'keep_old_revisions_for') != 0) {
                \Ip\Revision::removeOldRevisions($parametersMod->getValue('standard', 'configuration', 'advanced_options', 'keep_old_revisions_for'));
            }
        }        
        
        if ($options->firstTimeThisWeek) {
            $module = new Module();
            $systemInfo = $module->getSystemInfo();
            if ($systemInfo != '') { //send an email

                $md5 = \DbSystem::getSystemVariable('last_system_message_sent');

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

                    if (defined('ERRORS_SEND') && ERRORS_SEND != '') {
                        require_once(BASE_DIR . MODULE_DIR . 'administrator/email_queue/module.php');
                        $queue = new \Modules\administrator\email_queue\Module();
                        $queue->addEmail(
                        $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email'),
                        $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name'),
                        ERRORS_SEND,
                    '',
                        $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name'),
                        $message,
                        false,
                        true);
                        $queue->send();
                    }

                    \DbSystem::setSystemVariable('last_system_message_sent', md5($systemInfo));
                }



            }
        }

    }

}

