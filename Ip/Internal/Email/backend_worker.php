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
                    $fileNames = explode("\n", $email['fileNames']);
                    $fileMimeTypes = explode("\n", $email['fileMimeTypes']);
                    if(isset($_REQUEST['file_number'])) {
                        $number = $_REQUEST['file_number'];
                        if (isset($files[$number]) && isset($fileNames[$number]) && isset($fileMimeTypes[$number])) {
                            header('Content-type: '.$fileMimeTypes[$number]);
                            header("Content-Disposition: attachment; filename=\"".$fileNames[$number]."\";");
                            echo file_get_contents($files[$number]);
                        }
                    }


                    break;
            }

        }
    }
}

