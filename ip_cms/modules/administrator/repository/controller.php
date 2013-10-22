<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\administrator\repository;


/**
 * Repository controller to handle file uploads and supply
 * data about repository files to frontend.
 *
 * Files can be uploaded from frontend and backend.
 * But files from frontend can be uploaded only to
 * secured folder not accessible from the Internet
 */
class Controller extends \Ip\Controller{


    /**
     * Move files from temporary folder to repository.
     */
    public function storeNewFiles()
    {
        $this->backendOnly();

        if (!isset($_POST['files']) || !is_array($_POST['files'])) {
            $this->returnJson(array('status' => 'error', 'errorMessage' => 'Missing POST variable'));
        }

        $files = isset($_POST['files']) ? $_POST['files'] : array();


        foreach ($files as $key => $file) {
            if ($file['dir'] != TMP_FILE_DIR) {
                throw new \Exception("File is outside TMP dir.");
            }
        }


        $newFiles = array();

        $destination = BASE_DIR.FILE_REPOSITORY_DIR;
        foreach ($files as $key => $file) {
            $newName = \Library\Php\File\Functions::genUnoccupiedName($file['renameTo'], $destination);
            copy(BASE_DIR.$file['file'], $destination.$newName);
            unlink(BASE_DIR.$file['file']); //this is a temporary file
            $browserModel = \Modules\administrator\repository\BrowserModel::instance();
            $newFile = $browserModel->getFile($newName);
            $newFiles[] = $newFile;
        }
        $answer = array(
            'status' => 'success',
            'files' => $newFiles
        );

        $this->returnJson($answer);
    }


    /**
     * Upload file to temporary folder
     */
    public function upload()
    {

        $parametersMod = \Ip\ServiceLocator::getParametersMod();
        if (isset($_POST['secureFolder']) && $_POST['secureFolder']) {
            //upload to secure publicly not accessible folder.
            if ($parametersMod->getValue('administrator', 'repository', 'options', 'allow_anonymous_uploads')) {
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
                    $message = $parametersMod->getValue("developer", "form", "error_messages", "file_type");
                    break;
                case UploadException::FAILED_TO_MOVE_UPLOADED_FILE:
                case UploadException::NO_PERMISSION:
                case UploadException::INPUT_STREAM_ERROR:
                case UploadException::OUTPUT_STREAM_ERROR:
                default:
                    $log = \Ip\ServiceLocator::getLog();
                    $log->log('administrator/repository', 'File upload error', 'Error: '.$e->getMessage().' ('.$e->getCode().')');
                    $message = $parametersMod->getValue("developer", "form", "error_messages", "server");
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
            $this->returnJson($answer);
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

        $this->returnJson($answerArray);

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

        $this->returnJson($answer);
    }

    /**
     * @param string $url
     * @return string
     */
    protected function downloadFile($url, $title)
    {

        //download image to TMP dir and get $resultFilename
        $net = \Library\Php\Net::instance();
        $tmpDir = BASE_DIR.TMP_FILE_DIR;
        $tmpFilename = $net->downloadFile($url, $tmpDir, 'bigstock_'.time());
        if (!$tmpFilename) {
            return;
        }


        //find out file mime type to know required extension
        try {
            $mime = \Library\Php\File\Functions::getMimeType($tmpDir.$tmpFilename);
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
        $title = \Library\Php\File\Functions::cleanupFileName($title);
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
        $destinationDir = BASE_DIR.FILE_REPOSITORY_DIR;
        $destinationFileName = \Library\Php\File\Functions::genUnoccupiedName($niceFileName, $destinationDir);

        copy($tmpDir . $tmpFilename, $destinationDir . $destinationFileName);

        unlink($tmpDir . $tmpFilename);

        $browserModel = \Modules\administrator\repository\BrowserModel::instance();
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
            $this->returnJson($answer);
        }

        $file = realpath($_POST['file']);

        if (strpos($file, BASE_DIR.TMP_FILE_DIR) !== 0) {
            $answer = array(
                'status' => 'error',
                'error' => 'Trying to access file outside temporary dir'
            );
            $this->returnJson($answer);
        }


        if (file_exists($file)) {
            unlink($file);
        }

        $answer = array(
            'status' => 'success'
        );
        $this->returnJson($answer);
    }

    public function getAll()
    {
        $this->backendOnly();

        $seek = isset($_POST['seek']) ? (int) $_POST['seek'] : 0;
        $limit = 10000;
        $filter = isset($_POST['filter']) ? $_POST['filter'] : null;

        $browserModel = BrowserModel::instance();
        $files = $browserModel->getAvailableFiles($seek, $limit, $filter);

        usort ($files , array($this, 'sortFiles') );

        $fileGroups = array();
        foreach($files as $file) {
            $fileGroups[date("Y-m-d", $file['modified'])][] = $file;
        }


        $answer = array(
            'fileGroups' => $fileGroups
        );

        $this->returnJson($answer);
    }

    public function deleteFiles()
    {
        $this->backendOnly();

        $files = isset($_POST['files']) ? $_POST['files'] : null;
        $deletedFiles = array();
        $notRemovedCount = 0;

        foreach ($files as $file) {
            if ($this->removeFile($file['file'])) {
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

        $this->returnJson($answer);
    }

    private function removeFile($file)
    {

        $realFile = realpath($file);
        if (strpos($realFile, BASE_DIR . FILE_REPOSITORY_DIR) !== 0) {
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

    private function sortFiles($a, $b)
    {
        if ($a['modified'] == $b['modified']) {
            return 0;
        }
        return ($a['modified'] > $b['modified']) ? -1 : 1;
    }


    protected function backendOnly()
    {
        if (!\Ip\Backend::loggedIn()) {
            throw new \Exception('This controller can be accessed only by administrator');
        }
    }

}
