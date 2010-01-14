<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\community\newsletter;
 
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

require_once (__DIR__.'/db.php');
require_once (BASE_DIR.MODULE_DIR.'administrator/email_queue/module.php');


	class Actions{
		
		public static function makeActions($zoneName){
			global $site;
			global $parametersMod;
      global $log;

			$newsletterZone = $site->getZoneByModule('community', 'newsletter');

			if(!$newsletterZone)
				return;

      if (isset($_REQUEST['action'])) {
        switch ($_REQUEST['action']) {
          case 'subscribe':
    				if(isset($_REQUEST['email']) && Db::subscribed($_REQUEST['email'], $site->currentLanguage['id'])){
    					$status = 'subscribed';
    					$url = $site->generateUrl(null, $zoneName, array("subscribed"));
    				}elseif(!preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si', $_REQUEST['email'])){
    					$status = 'incorrect_email';
    					$url = $site->generateUrl(null, $zoneName, array("incorrect_email"));
            }else{
              require_once(__DIR__.'/template.php');
            
    					if($_REQUEST['email'] && !Db::registeredAndNotActivated($_REQUEST['email'], $site->currentLanguage['id']))
    						Db::subscribe($_REQUEST['email'], $site->currentLanguage['id']);
    					
    					$subscriber = Db::getSubscriberByEmail($_REQUEST['email'], $site->currentLanguage['id']); 
              
              
              $emailQueue = new \Modules\administrator\email_queue\Module();
    					$link = $site->generateUrl(null, $newsletterZone->getName(), array(), array("action" => "conf", "id" => $subscriber['id'], "code" => $subscriber['verification_code']));
              $emailHtml = Template::subscribeConfirmation($link); 
    					$emailQueue->addEmail(
    						$parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email'),
    						$parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email'), 
    						$_REQUEST['email'],
    						$_REQUEST['email'],
    						$parametersMod->getValue('community', 'newsletter', 'subscription_translations', 'subject_confirmation'),
    						$emailHtml,
    						true, true, null);
    					$emailQueue->send();
    					
    					$status = 'email_confirmation';
    					$url = $site->generateUrl(null, $zoneName, array("email_confirmation"));

    				}

  					echo '
            {
            "status":"'.$status.'",
            "url":"'.$url.'"
            }';

            $log->log('community/newsletter', 'Start subscribtion', $_REQUEST['email']);


            \Db::disconnect();
            exit;            
          break;
          case 'unsubscribe': //unsubscribtion through website
            if ($parametersMod->getValue('community', 'newsletter', 'options', 'show_unsubscribtion_button')) { //if unsubscribtion through webpage is allowed
      				Db::unsubscribe($_REQUEST['email'], $site->currentLanguage['id']);
    					echo '
              {
              "status":"email_confirmation",
              "url":"'.$site->generateUrl(null, $zoneName, array("unsubscribed")).'"
              }';
              
              $log->log('community/newsletter', 'Unsubscribtion from website', $_REQUEST['email']);
              
              \Db::disconnect();
              exit;            
            }
          break;
          case 'cancel': //unsubscribtion through e-mail link
            if (isset($_REQUEST['id']) && isset($_REQUEST['code'])) {
      				$record = DB::getSubscriber($_REQUEST['id']);
              $log->log('community/newsletter', 'Unsubscribtion from email', $record['email']);
    					
      				Db::unsubscribe($_REQUEST['email'], $site->currentLanguage['id'], $_REQUEST['id'], $_REQUEST['code']);
    					header('location: '.$site->generateUrl(null, $newsletterZone->getName(), array("unsubscribed"), array()));
    					
              \Db::disconnect();
              exit;            
            }
          break;
          case 'conf':
            if (isset($_GET['id']) && isset($_GET['code'])) {
            
      				if(Db::confirm($_GET['id'],  $_GET['code'], $site->currentLanguage['id'])) {
      					header('location: '.$site->generateUrl(null, $newsletterZone->getName(), array("subscribed"), array()));
        				$record = DB::getSubscriber($_GET['id']);
                $log->log('community/newsletter', 'Confirm subscribtion', $record['email']);
      				} else {
      					header('location: '.$site->generateUrl(null, $newsletterZone->getName(), array("error_confirmation"), array()));
                $log->log('community/newsletter', 'Incorrect confirmation link', $_GET['id'].' '.$_GET['code']);
      				}
            }
          break;
          case 'get_link':
            if (isset($_REQUEST['page'])) {
              switch ($_REQUEST['page']) {
                case 'error_confirmation':
                  echo $site->generateUrl(null, $zoneName, array("error_confirmation"));
                break;
                case 'email_confirmation':
                  echo $site->generateUrl(null, $zoneName, array("email_confirmation"));
                break;
                case 'subscribed':
                  echo $site->generateUrl(null, $zoneName, array("subscribed"));
                break;
                case 'incorrect_email':
                  echo $site->generateUrl(null, $zoneName, array("incorrect_email"));
                break;
                case 'unsubscribed':
                  echo $site->generateUrl(null, $zoneName, array("unsubscribed"));
                break;
              }
            }
            \Db::disconnect();
            exit;            
          break;
        }

      }
	 }
  }

		
   
