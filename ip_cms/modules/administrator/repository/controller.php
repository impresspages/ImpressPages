<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
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
            /*
             * plugin which uses repository had no chance to bind this file yet.
             * But repository requires all files to be bind. So repository automatically
             * binds all new files to itself. Later cron automatically unbinds all files
             * that are bind to repository. If file is not bind to any other module / plugin at that time,
             * file is removed.
             */
            \Modules\administrator\repository\Model::bindFile(FILE_DIR.$newName, 'administrator/repository', 0);

            unlink(BASE_DIR.$file['file']);
            $newFile = array(
                'fileName' => $newName,
                'dir' => $destination,
                'file' => FILE_REPOSITORY_DIR.$newName
            );
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
        $site = \Ip\ServiceLocator::getSite();

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
            $answer = array(
                'jsonrpc' => '2.0',
                'error' => array(
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'id' => 'id'
                )
            );
            $this->returnJson($answer);
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

        $limit = 100;


        $browserModel = BrowserModel::instance();
        $answer = $browserModel->getAvailableFiles($seek, $limit);


        $this->returnJson($answer);
    }


    protected function backendOnly()
    {
        if (!\Ip\Backend::loggedIn()) {
            throw new \Exception('This controller can be accessed only by administrator');
        }
    }

}
