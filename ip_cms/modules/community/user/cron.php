<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\community\user;

if (!defined('CMS')) exit;


require_once(__DIR__."/db.php");


class Cron {

    function execute($options) {
        global $parametersMod;
        global $dbSite;
        global $log;
        global $site;
        if($options->firstTimeThisMonth) {
            if($parametersMod->getValue('community', 'user', 'options', 'delete_expired_users')) {
                $soonOutdated = Db::getUsersToWarn($parametersMod->getValue('community', 'user', 'options', 'expires_in'), $parametersMod->getValue('community', 'user', 'options', 'warn_before'), $parametersMod->getValue('community', 'user', 'options', 'warn_every'));

                $queue = new \Ip\Module\Email\Module();

                $deleted = Db::deleteOutdatedUsers($parametersMod->getValue('community', 'user', 'options', 'expires_in'));

                foreach($deleted as $key => $user) {
                    $site->dispatchEvent('community', 'user', 'deleted_outdated_user', array('data'=>$user));
                    $emailTemplate = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email_template', $user['language_id']);
                    $email = str_replace('[[content]]', $parametersMod->getValue('community', 'user', 'email_messages', 'text_user_deleted'), $emailTemplate);
                    $email = str_replace('[[date]]', substr($user['last_login'], 0, 10), $email);
                    $queue->addEmail($parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email'), $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name'), $user['email'], '', $parametersMod->getValue('community', 'user', 'email_messages', 'subject_user_deleted'), $email, false, true);
                    $log->log('community/user', 'deleted account', 'Account information: '.implode(', ',$user));
                }


                $setWarned = array();

                foreach($soonOutdated as $key => $user) {
                    $site->dispatchEvent('community', 'user', 'warn_inactive_user', array('data'=>$user));
                    
                    $content = $parametersMod->getValue('community', 'user', 'email_messages', 'text_account_will_expire');
                    $content = str_replace('[[date]]', substr($user['valid_until'], 0, 10), $content);
                    $link = $site->generateUrl($user['language_id'], null, null, array("g"=>"community","m"=>"user", "a"=>"renewRegistration", "id"=>$user['id']));
                    $content = str_replace('[[link]]', '<a href="'.$link.'">'.$link.'</a>', $content);
                    
                    $websiteName = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name');
                    $websiteEmail = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email');
                    
                    $emailData = array(
                        'content' => $content,
                        'name' => $websiteName,
                        'email' => $websiteEmail
                    );
                    
                    $email = \Ip\View::create('view/email.php', $emailData, $user['language_id'])->render();
                    
                    $queue->addEmail($parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email'), $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name'), $user['email'], '', $parametersMod->getValue('community', 'user', 'email_messages', 'subject_account_will_expire'), $email, false, true);
                    $setWarned[] = $user['id'];
                    $log->log('community/user', 'account warned', 'Account information: '.implode(', ',$user));
                }

                Db::setWarned($setWarned);

                if((sizeof($deleted) > 0 || $soonOutdated > 0))
                $queue->send();
            }
        }
    }

}




