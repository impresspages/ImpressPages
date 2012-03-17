<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\community\user;

if (!defined('CMS')) exit;

require_once __DIR__.'/db.php';

require_once(LIBRARY_DIR."/php/form/standard.php");
require_once(MODULE_DIR."/administrator/email_queue/module.php");

class Actions {

    function makeActions() {
        global $site;
        global $parametersMod;
        global $session;
        global $log;


        $userZone = $site->getZoneByModule('community', 'user');
        if(!$userZone)
        return;


        if(isset($_REQUEST['action'])) {
            switch($_REQUEST['action']) {


                case 'renew_registration':
                    if(isset($_GET['id'])) {
                        if(Db::renewRegistration($_GET['id']) == 1){
                            $site->dispatchEvent('community', 'user', 'renew_registration', array('user_id'=>$_GET['id']));
                            header('location: '.$site->generateUrl(null, $userZone->getName(), array(Config::$urlRenewedRegistration)));
                        } else {
                            header('location: '.$site->generateUrl(null, $userZone->getName(), array(Config::$urlRenewRegistrationError)));
                        }
                    }else
                    header('location: '.$site->generateUrl(null, $userZone->getName(), array(Config::$urlRenewRegistrationError)));
                    \Db::disconnect();
                    exit;
                    break;
            }

        }

    }









}
