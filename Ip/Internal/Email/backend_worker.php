<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Email;

require_once(__DIR__ . '/db.php');
//TODOXX REFACTOR #132

class BackendWorker {

    function work() {
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

