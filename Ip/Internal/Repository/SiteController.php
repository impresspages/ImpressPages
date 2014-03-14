<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Repository;


/**
 * Repository controller to handle file uploads and supply
 * data about repository files to frontend.
 *
 * Files can be uploaded from frontend and backend.
 * But files from frontend can be uploaded only to
 * secured folder not accessible from the Internet
 */
class SiteController extends \Ip\Controller{




    /**
     * Upload file to temporary folder
     */
    public function upload()
    {

        if (isset($_POST['secureFolder']) && $_POST['secureFolder']) {
            //upload to secure publicly not accessible folder.
            if (!ipGetOption('Config.allowAnonymousUploads', 1)) {
                throw new \Exception('Anonymous uploads are not enabled. You can enable them in config.');
            } else {
                //do nothing. Anonymous uploads are allowed to secure folder
            }
            $secureFolder = true;
        } else {
            $secureFolder = false;
            $this->backendOnly();
        }


        $uploadModel = UploadModel::instance();
        try {
            $uploadModel->handlePlupload($secureFolder);
        } catch (UploadException $e) {
            // Return JSON-RPC response

            switch($e->getCode()){
                case UploadException::FORBIDDEN_FILE_EXTENSION:
                    $message = __('Incorrect file type.', 'ipAdmin');
                    ipLog()->info('Repository.invalidUploadedFileExtension: ' . $e->getMessage(), array('plugin' => 'Repository'));
                    break;
                case UploadException::FAILED_TO_MOVE_UPLOADED_FILE:
                case UploadException::NO_PERMISSION:
                case UploadException::INPUT_STREAM_ERROR:
                case UploadException::OUTPUT_STREAM_ERROR:
                default:
                    ipLog()->error('Repository.fileUploadError', array('plugin' => 'Repository', 'exception' => $e));
                    $message = __('Can\'t store uploaded file. Please check server configuration.', 'ipAdmin');
                    break;

            }

            // TODO JSONRPC
            $answer = array(
                'jsonrpc' => '2.0',
                'error' => array(
                    'code' => $e->getCode(),
                    'message' => $message,
                    'id' => 'id'
                )
            );
            return new \Ip\Response\Json($answer);
            return;
        }
        $fileName = $uploadModel->getUploadedFileName();
        $file = $uploadModel->getUploadedFile();
        $targetDir = $uploadModel->getTargetDir();


        // Return JSON-RPC response
        $answerArray = array(
            "jsonrpc" => "2.0",
            "result" => null,
            "id" => "id",
            "fileName" => $fileName
        );

        if (!$secureFolder) {
            $answerArray['dir'] = $targetDir;
            $answerArray['file'] = $file;
        }

        return new \Ip\Response\Json($answerArray);

    }



    /**
     * @param string $url
     * @return string
     */
    protected function downloadFile($url, $title)
    {

        //download image to TMP dir and get $resultFilename
        $net = \Ip\Internal\NetHelper::instance();
        $tmpFilename = $net->downloadFile($url, ipFile('file/tmp/'), 'bigstock_'.time());
        if (!$tmpFilename) {
            return;
        }


        //find out file mime type to know required extension
        try {
            $mime = \Ip\Internal\File\Functions::getMimeType(ipFile('file/tmp/' . $tmpFilename));
            switch($mime) {
                case 'image/png':
                    $ext = '.jpg';
                    break;
                case 'image/gif':
                    $ext = '.gif';
                    break;
                case 'image/bmp':
                    $ext = '.bmp';
                    break;
                case 'image/pjpeg':
                case 'image/jpeg':
                default:
                    $ext = '.jpg';
                    break;
            }

        } catch (\Ip\PhpException $e) {
            $ext = '.jpg';
        }

        //get real nice new file name
        $title = \Ip\Internal\File\Functions::cleanupFileName($title);
        $words = explode(' ', $title);
        $cleanTitle = '';
        foreach($words as $word) { //limit file name to 30 symbols
            if (strlen($cleanTitle.'_'.$word) > 30) {
                break;
            }
            if ($cleanTitle != '') {
                $cleanTitle .= '_';
            }
            $cleanTitle .= $word;
        }
        if ($cleanTitle == '') {
            $cleanTitle = 'file';
        }

        $niceFileName = $cleanTitle.$ext;
        $destinationDir = ipFile('file/repository/');
        $destinationFileName = \Ip\Internal\File\Functions::genUnoccupiedName($niceFileName, $destinationDir);

        copy(ipFile('file/tmp/' . $tmpFilename), $destinationDir . $destinationFileName);

        unlink(ipFile('file/tmp/' . $tmpFilename));

        $browserModel = \Ip\Internal\Repository\BrowserModel::instance();
        $file = $browserModel->getFile($destinationFileName);
        return $file;
    }






    protected function backendOnly()
    {
        if (!ipAdminId()) {
            throw new \Exception('This controller can be accessed only by administrator');
        }
    }

}
