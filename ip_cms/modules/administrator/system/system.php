<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\administrator\system;
if (!defined('CMS')) exit;


class System{

    function init(){
//        global $site;
//        if ($site->managementState()) { //create required revisions.
//            $lastRevision = \Ip\Db::getLastRevision($site->getCurrentZone()->getName(), $site->getCurrentElement()->getId());
//            if ($lastRevision === false) {
//                $revisionId = \Ip\Db::createRevision($site->getCurrentZone()->getName(), $site->getCurrentElement()->getId(), 1); //create published revision
//                \Ip\Db::duplicateRevision($revisionId); //create one more revision on whish we will work on
//            } else {
//                if ($lastRevision['published']) {
//                    \Ip\Db::duplicateRevision($lastRevision['revisionId']); //create one more revision on whish we will work on
//                }    
//            }
//        }
    }
}            
        

