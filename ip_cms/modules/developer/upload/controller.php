<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\developer\upload;
if (!defined('CMS')) exit;



class Controller extends \Ip\Controller{


    public function allowAction($action) {
        if (\Ip\Backend::loggedIn()) {
            return \Ip\Backend::userHasPermission(\Ip\Backend::userId(), 'standard', 'content_management');
        } else {
            return false;
        }
    }

    public function getImageContainerHtml() {
        global $site;
        $html = \Ip\View::create('view/imageContainer.php', array())->render();
        $site->setOutput($html);
        $answerArray = array(
            "status" => "success",
            "html" => $html
        );
        $answer = json_encode($answerArray);
        $site->setOutput($answer);
    }


    public function getFileContainerHtml() {
        global $site;
        $html = \Ip\View::create('view/fileContainer.php', array())->render();
        $site->setOutput($html);
        $answerArray = array(
            "status" => "success",
            "html" => $html
        );
        $answer = json_encode($answerArray);
        $site->setOutput($answer);
    }

    public function upload(){
        global $site;

        if (!isset($_SESSION['backend_session']['user_id'])) {
            die('{"jsonrpc" : "2.0", "error" : {"code": 201, "message": "Try to upload image to temporary directory without permission."}, "id" : "id"}');
        }

        // Settings
        //$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        $targetDir = BASE_DIR.TMP_FILE_DIR;

        //$cleanupTargetDir = false; // Remove old files
        //$maxFileAge = 60 * 60; // Temp file age in seconds

        // Get parameters
        $chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
        $chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

        // Clean the fileName for security reasons
        $fileName = preg_replace('/[^\w\._]+/', '', $fileName);

        // Make sure the fileName is unique but only if chunking is disabled
        if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
            $ext = strrpos($fileName, '.');
            $fileName_a = substr($fileName, 0, $ext);
            $fileName_b = substr($fileName, $ext);

            $count = 1;
            while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
            $count++;

            $fileName = $fileName_a . '_' . $count . $fileName_b;
        }


        //security check Mangirdas 2011-12-15
        $fileExtension = strtolower(substr($fileName, strrpos($fileName, '.') + 1));
        $disallow = array('htaccess','php', 'php2','php3','php4','php5','php6','cfm','cfc','bat','exe','com','dll','vbs','js','reg','asis','phtm','phtml','pwml','inc','pl','py','jsp','asp','aspx','ascx','shtml','sh','cgi', 'cgi4', 'pcgi', 'pcgi5');
        if (in_array($fileExtension, $disallow)) {
            //security risk
            die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Forbidden file extension: '.$fileExtension.'."}, "id" : "id"}');
        }
        
        //end security check


        // Look for the content type header
        if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
        $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

        if (isset($_SERVER["CONTENT_TYPE"]))
        $contentType = $_SERVER["CONTENT_TYPE"];

        // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
        if (strpos($contentType, "multipart") !== false) {
            if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                // Open temp file
                $out = fopen($targetDir . $fileName, $chunk == 0 ? "wb" : "ab");
                if ($out) {
                    // Read binary input stream and append it to temp file
                    $in = fopen($_FILES['file']['tmp_name'], "rb");

                    if ($in) {
                        while ($buff = fread($in, 4096))
                        fwrite($out, $buff);
                    } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                    fclose($in);
                    fclose($out);
                    @unlink($_FILES['file']['tmp_name']);
                } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            } else
            die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
        } else {
            // Open temp file
            $out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
            if ($out) {
                // Read binary input stream and append it to temp file
                $in = fopen("php://input", "rb");

                if ($in) {
                    while ($buff = fread($in, 4096)) {
                        if(function_exists('set_time_limit'))
                        {
                            set_time_limit(30);
                        }
                        fwrite($out, $buff);
                    }
                } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

                fclose($in);
                fclose($out);
            } else
            die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }

        // Return JSON-RPC response
        $answerArray = array(
            "jsonrpc" => "2.0",
            "result" => null, 
            "id" => "id",
            "fileName" => TMP_FILE_DIR.$fileName
        );
        $answer =  json_encode($answerArray);
        $site->setOutput($answer);
        //die('{"jsonrpc" : "2.0", "result" : , "id" : "id"}');

    }



     

}
