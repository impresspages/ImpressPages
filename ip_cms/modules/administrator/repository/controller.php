<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
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

        $site = \Ip\ServiceLocator::getSite();

        if (!isset($_POST['files']) || !is_array($_POST['files'])) {
            throw new \Ip\CoreException('Invalid parameters.');
        }
        $files = $_POST['files'];

        if (function_exists('set_time_limit')) {
            set_time_limit(count($files) * 60 + 30);
        }

        $answer = array();
        foreach($files as $file) {
            if (!empty($file['url']) && !empty($file['name'])) {
                $answer[] = $this->downloadFile($file['url'], $file['name']);
            }

        }

        $this->returnJson($answer);


    }

    /**
     * @param string $url
     * @return string
     */
    protected function downloadFile($url, $desiredFilename)
    {
        // validate filename
        $desiredFilename = pathinfo($desiredFilename, PATHINFO_BASENAME);
        if ($desiredFilename == '.') {
            $desiredFilename = pathinfo($desiredFilename, PATHINFO_BASENAME);
            if ($desiredFilename == '.') {
                $desiredFilename = 'file';
            }
        }

        $desiredFilename = \Library\Php\File\Functions::genUnoccupiedName($desiredFilename, BASE_DIR . TMP_FILE_DIR);
        $tmpPath = BASE_DIR . TMP_FILE_DIR . $desiredFilename;

        $net = new \Modules\administrator\system\Helper\Net();  // TODOX pažeistas modulių atsietumo principas
        $net->downloadFile($url, $tmpPath);

        $destinationDir = BASE_DIR.FILE_REPOSITORY_DIR;
        $filename = \Library\Php\File\Functions::genUnoccupiedName($tmpPath, $destinationDir);
        copy($tmpPath, $destinationDir . $filename);

        unlink($tmpPath);

        $browserModel = \Modules\administrator\repository\BrowserModel::instance();

        $file = $browserModel->getFile($filename);
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

        if(isset($_POST['seek'])) {
            $seek = (int) $_POST['seek'];
        } else {
            $seek = 0;
        }

        $limit = 10000;


        $browserModel = BrowserModel::instance();
        $files = $browserModel->getAvailableFiles($seek, $limit);

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

    private function sortFiles($a, $b) {
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
