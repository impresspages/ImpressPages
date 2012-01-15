<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Modules\community\newsletter;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

require_once (__DIR__.'/db.php');
require_once (BASE_DIR.MODULE_DIR.'administrator/email_queue/module.php');


class Controller  extends \Ip\Controller{


    public function __construct() {

    }

    public function subscribe() {
        global $site;
        global $parametersMod;
        global $log;
        $newsletterZone = $site->getZoneByModule('community', 'newsletter');

        if(!$newsletterZone)
        return;

        if(isset($_REQUEST['email']) && Db::subscribed($_REQUEST['email'], $site->currentLanguage['id'])) {
            $status = 'success';
            $step = 'subscribed';
            $url = $site->generateUrl(null, $zoneName, array("subscribed"));
        }elseif(!preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si', $_REQUEST['email'])) {
            $this->_errorAnswer($parametersMod->getValue('community', 'newsletter', 'subscription_translations', 'text_incorrect_email'));
            return;
        }else {
            $site->requireTemplate('community/newsletter/template.php');

            if($_REQUEST['email'] && !Db::registeredAndNotActivated($_REQUEST['email'], $site->currentLanguage['id']))
            Db::subscribe($_REQUEST['email'], $site->currentLanguage['id']);

            $subscriber = Db::getSubscriberByEmail($_REQUEST['email'], $site->currentLanguage['id']);


            $emailQueue = new \Modules\administrator\email_queue\Module();
            $link = $site->generateUrl(null, $newsletterZone->getName(), array(), array("action" => "conf", "id" => $subscriber['id'], "code" => $subscriber['verification_code']));
            $emailHtml = Template::subscribeConfirmation($link);
            $emailQueue->addEmail(
            $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email'),
            $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name'),
            $_REQUEST['email'],
                    '',
            $parametersMod->getValue('community', 'newsletter', 'subscription_translations', 'subject_confirmation'),
            $emailHtml,
            true, true, null);
            $emailQueue->send();

            $status = 'success';
            $step = 'email_confirmation';
            $url = $site->generateUrl(null, $zoneName, array("email_confirmation"));

        }

        $data = array (
            'status' => $status,
            'step' => $step,
            'redirectUrl' => $url
        );
        
        $log->log('community/newsletter', 'Start subscribtion', $_REQUEST['email']);
        
        $this->_outputAnswer($data);

    }

    /**
     *
     * Unsubscribe via website
     */
    public function unsubscribe() { //unsubscribe via website
        global $site;
        global $parametersMod;
        global $log;

        $newsletterZone = $site->getZoneByModule('community', 'newsletter');

        if(!$newsletterZone)
        return;

        if ($parametersMod->getValue('community', 'newsletter', 'options', 'show_unsubscribe_button')) { //if unsubscribe through webpage is allowed
            Db::unsubscribe($_REQUEST['email'], $site->currentLanguage['id']);
            echo '
              {
              "status":"email_confirmation",
              "url":"'.$site->generateUrl(null, $zoneName, array("unsubscribed")).'"
              }';

            $log->log('community/newsletter', 'Unsubscribe (website form)', $_REQUEST['email']);
        }

    }

    /**
     *
     * Unsubscribe via e-mail link
     */
    public function cancel() {
        global $site;
        global $parametersMod;
        global $log;
        $newsletterZone = $site->getZoneByModule('community', 'newsletter');

        if(!$newsletterZone)
        return;

        if (isset($_REQUEST['id']) && isset($_REQUEST['code'])) {
            $record = DB::getSubscriber($_REQUEST['id']);
            $log->log('community/newsletter', 'Unsubscribe (e-mail link)', $record['email']);

            Db::unsubscribe($_REQUEST['email'], $site->currentLanguage['id'], $_REQUEST['id'], $_REQUEST['code']);
            header('location: '.$site->generateUrl(null, $newsletterZone->getName(), array("unsubscribed"), array()));

            \Db::disconnect();
            exit;
        }
    }

    public function getLink() {
        global $site;
        global $parametersMod;
        global $log;
        $newsletterZone = $site->getZoneByModule('community', 'newsletter');

        if(!$newsletterZone)
        return;

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

    /**
     * 
     * Confirm subscribtion
     */
    public function conf () {
        global $site;
        global $parametersMod;
        global $log;
        $newsletterZone = $site->getZoneByModule('community', 'newsletter');

        if(!$newsletterZone)
        return;

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
    }
    
    private function _errorAnswer($errorMessage) {
        $data = array (
            'status' => 'error',
            'errorMessage' => $errorMessage
        );

        $this->_outputAnswer($data);
    }

    private function _outputAnswer($data) {
        global $site;
        //header('Content-type: text/json; charset=utf-8'); throws save file dialog on firefox if iframe is used
        $answer = json_encode($data);
        $site->setOutput($answer);
    }    

}



