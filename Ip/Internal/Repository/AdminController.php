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
class AdminController extends \Ip\Controller
{


    /**
     * Move files from temporary folder to repository.
     */
    public function storeNewFiles()
    {
        ipRequest()->mustBePost();
        $post = ipRequest()->getPost();
        $secure = !empty($post['secure']);
        $path = isset($post['path']) ? $post['path'] : null;


        $browserModel = BrowserModel::instance();

        $browserModel->pathMustBeInRepository($path, $secure);


        if (!isset($post['files']) || !is_array($post['files'])) {
            return new \Ip\Response\Json(array('status' => 'error', 'errorMessage' => 'Missing POST variable'));
        }

        $files = isset($post['files']) ? $post['files'] : [];

        $newFiles = [];


        $destination = $browserModel->getPath($secure, $path);


        foreach ($files as $file) {
            $sourceDir = 'file/tmp/';
            if ($secure) {
                $sourceDir = 'file/secure/tmp/';
            }


            $source = ipFile($sourceDir . $file['fileName']);
            $source = realpath($source); //to avoid any tricks with relative paths, etc.
            if (strpos($source, realpath(ipFile($sourceDir))) !== 0) {
                ipLog()->alert('Core.triedToAccessNonPublicFile', array('file' => $file['fileName']));
                continue;
            }


            $newName = \Ip\Internal\File\Functions::genUnoccupiedName($file['renameTo'], $destination);
            copy($source, $destination . $newName);
            unlink($source); //this is a temporary file
            $browserModel = \Ip\Internal\Repository\BrowserModel::instance();
            $newFile = $browserModel->getFile($newName, $secure, $path);
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
        $query = ipRequest()->getQuery();
        $seek = isset($query['seek']) ? (int)$query['seek'] : 0;
        $limit = 10000;
        $filter = isset($query['filter']) ? $query['filter'] : null;
        $filterExtensions = $query['filterExtensions'] ? $query['filterExtensions'] : null;
        if (is_string($filterExtensions)) {
            $filterExtensions = array($filterExtensions);
        }
        $secure = isset($query['secure']) ? (int)$query['secure'] : null;
        $path = isset($query['path']) ? $query['path'] : null;

        $browserModel = BrowserModel::instance();

        $browserModel->pathMustBeInRepository($path, $secure);

        $files = $browserModel->getAvailableFiles($seek, $limit, $filter, $filterExtensions, $secure, $path);

        usort($files, array($this, 'sortFiles'));

        $fileGroups = [];
        foreach ($files as $file) {
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
        ipRequest()->mustBePost();
        $post = ipRequest()->getPost();
        $secure = !empty($post['secure']);

        $files = isset($post['files']) ? $post['files'] : null;
        $deletedFiles = [];
        $notRemovedCount = 0;

        $forced = ipRequest()->getPost('forced', false);

        foreach ($files as $file) {
            if (isset($file['fileName']) && $this->removeFile($file['fileName'], $secure, $forced)) {
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


    private function removeFile($file, $secure, $forced = false)
    {
        if (basename($file) == '.htaccess') {
            //for security reasons we don't allow to remove .htaccess files
            return false;
        }

        $baseDir = 'file/repository/';
        if ($secure) {
            $baseDir = 'file/secure/';
        }
        $realFile = realpath(ipFile($baseDir . $file));
        if (strpos($realFile, realpath(ipFile($baseDir))) !== 0) {
            return false;
        }

        $model = Model::instance();
        $usages = $model->whoUsesFile($file);
        if (!$forced && !empty($usages)) {
            return false;
        }

        if (!$secure) {
            $reflectionModel = ReflectionModel::instance();
            $reflectionModel->removeReflections($file);
        }

        if (file_exists($realFile) && is_file($realFile) && is_writable($realFile)) {
            unlink($realFile);
        }
        return true;
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

        if (!ipAdminPermission('Repository upload')) {
            throw new \Ip\Exception("Permission denied");
        }

        if (!isset($_POST['files']) || !is_array($_POST['files'])) {
            throw new \Ip\Exception('Invalid parameters.');
        }
        $files = $_POST['files'];

        if (function_exists('set_time_limit')) {
            set_time_limit(count($files) * 60 + 30);
        }

        $answer = [];
        foreach ($files as $file) {
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
        $net = new \Ip\Internal\NetHelper();
        $tmpFilename = $net->downloadFile($url, ipFile('file/tmp/'), 'bigstock_' . time());
        if (!$tmpFilename) {
            return null;
        }


        //find out file mime type to know required extension
        try {
            $mime = \Ip\Internal\File\Functions::getMimeType(ipFile('file/tmp/' . $tmpFilename));
            switch ($mime) {
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
        foreach ($words as $word) { //limit file name to 30 symbols
            if (strlen($cleanTitle . '_' . $word) > 30) {
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

        $niceFileName = $cleanTitle . $ext;
        $destinationDir = ipFile('file/repository/');
        $destinationFileName = \Ip\Internal\File\Functions::genUnoccupiedName($niceFileName, $destinationDir);

        copy(ipFile('file/tmp/' . $tmpFilename), $destinationDir . $destinationFileName);

        unlink(ipFile('file/tmp/' . $tmpFilename));

        $browserModel = \Ip\Internal\Repository\BrowserModel::instance();
        $file = $browserModel->getFile($destinationFileName);
        return $file;
    }


}
