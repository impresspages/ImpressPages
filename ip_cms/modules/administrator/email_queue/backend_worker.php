<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\administrator\email_queue; 
if (!defined('BACKEND')) exit;  


require_once (__DIR__.'/db.php');


class BackendWorker {

  function work() {
    global $site;
    global $parametersMod;
    if (isset($_REQUEST['action'])) {
      switch ($_REQUEST['action']) {
        case 'preview':
          $email = Db::getEmail($_REQUEST['record_id']);
          echo $email['email'];
          break;
        case 'get_file':
          $email = Db::getEmail($_REQUEST['record_id']);
          $files = explode("\n", $email['files']);
          $file_names = explode("\n", $email['file_names']);
          $file_mime_types = explode("\n", $email['file_mime_types']);
          if(isset($_REQUEST['file_number'])) {
            $number = $_REQUEST['file_number'];
            if (isset($files[$number]) && isset($file_names[$number]) && isset($file_mime_types[$number])) {
              header('Content-type: '.$file_mime_types[$number]);
              header("Content-Disposition: attachment; filename=\"".$file_names[$number]."\";");
              echo file_get_contents($files[$number]);
            }
          }


          break;
      }

    }
  }
}

