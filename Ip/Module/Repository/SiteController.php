<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Repository;


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

        $parametersMod = \Ip\ServiceLocator::getParametersMod();
        if (isset($_POST['secureFolder']) && $_POST['secureFolder']) {
            //upload to secure publicly not accessible folder.
            if ($parametersMod->getValue('Repository.allow_anonymous_uploads')) {
                //do nothing. Anonymous uploads are allowed to secure folder
            } else {
                throw new \Exception('Anonymous uploads are not enabled. You can enable them in config.');
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
                    $message = $parametersMod->getValue("Form.file_type");
                    break;
                case UploadException::FAILED_TO_MOVE_UPLOADED_FILE:
                case UploadException::NO_PERMISSION:
                case UploadException::INPUT_STREAM_ERROR:
                case UploadException::OUTPUT_STREAM_ERROR:
                default:
                    $log = \Ip\ServiceLocator::log();
                    $log->log('administrator/repository', 'File upload error', 'Error: '.$e->getMessage().' ('.$e->getCode().')');
                    $message = $parametersMod->getValue("Form.server");
                    break;

            }

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
     * Downloads file from $_POST['url'] and stores it in repository as $_POST['filename']. If desired filename is taken,
     * selects some alternative unoccupied name.
     *
     * Outputs repository file properties in JSON format.
     *
     * @throws \Ip\CoreException
     */
    public function addFromUrl()
    {

        $this->backendOnly();

        if (!isset($_POST['files']) || !is_array($_POST['files'])) {
            throw new \Ip\CoreException('Invalid parameters.');
        }
        $files = $_POST['files'];

        if (function_exists('set_time_limit')) {
            set_time_limit(count($files) * 60 + 30);
        }

        $answer = array();
        foreach($files as $file) {
            if (!empty($file['url']) && !empty($file['title'])) {
                $fileData = $this->downloadFile($file['url'], $file['title']);
                if ($fileData) {
                    $answer[] = $fileData;
                }

            }

        }

        return new \Ip\Response\Json($answer);
    }

    /**
     * @param string $url
     * @return string
     */
    protected function downloadFile($url, $title)
    {

        //download image to TMP dir and get $resultFilename
        $net = \Ip\Internal\NetHelper::instance();
        $tmpFilename = $net->downloadFile($url, ipConfig()->temporaryFile(''), 'bigstock_'.time());
        if (!$tmpFilename) {
            return;
        }


        //find out file mime type to know required extension
        try {
            $mime = \Ip\Internal\File\Functions::getMimeType(ipConfig()->temporaryFile($tmpFilename));
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
        $destinationDir = ipConfig()->repositoryFile('');
        $destinationFileName = \Ip\Internal\File\Functions::genUnoccupiedName($niceFileName, $destinationDir);

        copy(ipConfig()->temporaryFile($tmpFilename), $destinationDir . $destinationFileName);

        unlink(ipConfig()->temporaryFile($tmpFilename));

        $browserModel = \Ip\Module\Repository\BrowserModel::instance();
        $file = $browserModel->getFile($destinationFileName);
        return $file;
    }

    public function deleteTmpFile()
    {
        $this->backendOnly();

        if (!isset($_POST['file'])) {
            $answer = array(
                'status' => 'error',
                'error' => 'Missing post variable'
            );
            return new \Ip\Response\Json($answer);
        }

        $file = realpath($_POST['file']);

        if (strpos($file, ipConfig()->temporaryFile('')) !== 0) {
            $answer = array(
                'status' => 'error',
                'error' => 'Trying to access file outside temporary dir'
            );
            return new \Ip\Response\Json($answer);
        }


        if (file_exists($file)) {
            unlink($file);
        }

        $answer = array(
            'status' => 'success'
        );
        return new \Ip\Response\Json($answer);
    }


    public function deleteFiles()
    {
        $this->backendOnly();

        $files = isset($_POST['files']) ? $_POST['files'] : null;
        $deletedFiles = array();
        $notRemovedCount = 0;

        foreach ($files as $file) {
            if (isset($file['file']) && $this->removeFile($file['file'])) {
                $deletedFiles[] = $file['file'];
            } else {
                $notRemovedCount++;
            }
        }

        $answer = array(
            'success' => true,
            'deletedFiles' => $deletedFiles,
            'notRemovedCount' => $notRemovedCount
        );

        return new \Ip\Response\Json($answer);
    }

    private function removeFile($file)
    {
        if (basename($file) == '.htaccess') {
            //for security reasons we don't allow to remove .htaccess files
            return false;
        }
        
        $realFile = realpath($file);
        if (strpos($realFile, ipConfig()->repositoryFile('')) !== 0) {
            return false;
        }

        $model = Model::instance();
        $usages = $model->whoUsesFile($file);
        if (!empty($usages)) {
            return false;
        }

        $reflectionModel = ReflectionModel::instance();
        $reflectionModel->removeReflections($file);

        if (file_exists($realFile) && is_file($realFile) && is_writable($realFile)) {
            unlink($realFile);
        }
        return true;
    }



    protected function backendOnly()
    {
        if (!\Ip\Module\Admin\Backend::loggedIn()) {
            throw new \Exception('This controller can be accessed only by administrator');
        }
    }

}
