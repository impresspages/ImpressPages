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
class AdminController extends \Ip\Controller{


    /**
     * Move files from temporary folder to repository.
     */
    public function storeNewFiles()
    {

        if (!isset($_POST['files']) || !is_array($_POST['files'])) {
            return new \Ip\Response\Json(array('status' => 'error', 'errorMessage' => 'Missing POST variable'));
        }

        $files = isset($_POST['files']) ? $_POST['files'] : array();

        $newFiles = array();

        $destination = ipFile('file/repository/');
        foreach ($files as $file) {
            basename($file['fileName']); //to avoid any tricks with relative paths, etc.
            $newName = \Ip\Internal\File\Functions::genUnoccupiedName($file['renameTo'], $destination);
            copy(ipFile('file/tmp/' . $file['fileName']), $destination.$newName);
            unlink(ipFile('file/tmp/' . $file['fileName'])); //this is a temporary file
            $browserModel = \Ip\Internal\Repository\BrowserModel::instance();
            $newFile = $browserModel->getFile($newName);
            $newFiles[] = $newFile;
        }
        $answer = array(
            'status' => 'success',
            'files' => $newFiles
        );

        return new \Ip\Response\Json($answer);
    }


    public function getAll()
    {

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

        return new \Ip\Response\Json($answer);
    }

    private function sortFiles($a, $b)
    {
        if ($a['modified'] == $b['modified']) {
            return 0;
        }
        return ($a['modified'] > $b['modified']) ? -1 : 1;
    }


    public function deleteFiles()
    {


        $files = isset($_POST['files']) ? $_POST['files'] : null;
        $deletedFiles = array();
        $notRemovedCount = 0;

        foreach ($files as $file) {
            if (isset($file['fileName']) && $this->removeFile($file['fileName'])) {
                $deletedFiles[] = $file['fileName'];
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

        $realFile = realpath(ipFile('file/repository/' . $file));
        if (strpos($realFile, ipFile('file/repository/')) !== 0) {
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

        if (strpos($file, ipFile('file/tmp/')) !== 0) {
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

    /**
     * Downloads file from $_POST['url'] and stores it in repository as $_POST['filename']. If desired filename is taken,
     * selects some alternative unoccupied name.
     *
     * Outputs repository file properties in JSON format.
     *
     * @throws \Ip\Exception
     */
    public function addFromUrl()
    {

        $this->backendOnly();

        if (!isset($_POST['files']) || !is_array($_POST['files'])) {
            throw new \Ip\Exception('Invalid parameters.');
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

}
