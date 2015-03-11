<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Repository;


/**
 *
 * Centralized repository to store files. Often the same image needs to be used by many
 * modules / widgets. This class handles these dependencies. Use this module to add new files to global
 * files repository. Bind new modules to already existing files. When the file is not bind to any module,
 * it is automatically removed. So bind to existing files, unbind from them and don't worry if some other
 * modules uses the same files. This class will take care.
 *
 */
class UploadModel
{

    protected $uploadedFileName;
    protected $uploadedFile;
    protected $targetDir;

    protected function __construct()
    {

    }

    protected function __clone()
    {

    }

    /**
     * @return UploadModel
     */
    public static function instance()
    {
        return new UploadModel();
    }

    /**
     * Handle uploads made using PlUpload library
     * @param bool $secureFolder
     * @throws \Ip\Exception\Repository\Upload
     */
    public function handlePlupload($secureFolder)
    {
        if (!$secureFolder && !ipAdminId()) {
            throw new \Ip\Exception\Repository\Upload("Trying to upload image to temporary directory without permission.");
        }

        if ($secureFolder) {
            $targetDir = ipFile('file/secure/tmp/');
        } else {
            $targetDir = ipFile('file/tmp/');
        }

        if ($secureFolder) {
            $sizeLimit = ipGetOption('Repository.publicUploadLimit', 4000);
            if ($this->folderSize($targetDir) > $sizeLimit * 1000000) { //4000 Mb by default
                ipLog()->error(
                    "Repository.publicUploadLimitReached: IP: `{ip}`. CurrentLimit `{limit}Mb`. Please update Repository.publicUploadLimit option to increase the limits.",
                    array('ip' => $_SERVER['REMOTE_ADDR'], 'limit' => $sizeLimit)
                );
                throw new \Ip\Exception("Upload limit reached");
            }

        }


        // Get parameters
        $chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
        $chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

        // Clean the fileName for security reasons
        $fileName = \Ip\Internal\File\Functions::cleanupFileName($fileName);

        // Make sure the fileName is unique but only if chunking is disabled
        if ($chunks < 2 && file_exists($targetDir . $fileName)) {
            $fileName = \Ip\Internal\File\Functions::genUnoccupiedName($fileName, $targetDir);
        }


        //security check
        $fileExtension = strtolower(substr($fileName, strrpos($fileName, '.') + 1));

        $whiteListExtensions = array(
            'jpg',
            'jpeg',
            'jpe',
            'gif',
            'png',
            'bmp',
            'tif',
            'tiff',
            'ico',
            'asf',
            'asx',
            'wmv',
            'wmx',
            'wm',
            'avi',
            'divx',
            'flv',
            'mov',
            'qt',
            'mpeg',
            'mpg',
            'mpe',
            'mp4',
            'm4v',
            'ogv',
            'webm',
            'mkv',
            'txt',
            'asc',
            'c',
            'cc',
            'h',
            'csv',
            'tsv',
            'ics',
            'rtx',
            'css',
            'htm',
            'html',
            'vtt',
            'mp3',
            'm4a',
            'm4b',
            'ra',
            'ram',
            'wav',
            'ogg',
            'oga',
            'mid',
            'midi',
            'wma',
            'wax',
            'mka',
            'rtf',
            'js',
            'pdf',
            'class',
            'tar',
            'zip',
            'gz',
            'gzip',
            'rar',
            '7z',
            'doc',
            'pot',
            'pps',
            'ppt',
            'wri',
            'xla',
            'xls',
            'xlt',
            'xlw',
            'mdb',
            'mpp',
            'docx',
            'docm',
            'dotx',
            'dotm',
            'eps',
            'xlsx',
            'xlsm',
            'xlsb',
            'xltx',
            'xltm',
            'xlam',
            'pptx',
            'pptm',
            'ppsx',
            'ppsm',
            'potx',
            'potm',
            'ppam',
            'sldx',
            'sldm',
            'onetoc',
            'onetoc2',
            'onetmp',
            'onepkg',
            'odt',
            'odp',
            'ods',
            'odg',
            'odc',
            'odb',
            'odf',
            'wp',
            'wpd',
            'key',
            'numbers',
            'pages',
            'xml',
            'json',
            'iso',
            'aac',
            'img',
            'psd',
            'ai',
            'sql',
            'swf',
            'svg'
        );
        $whiteListExtensions = ipFilter('ipWhiteListExtensions', $whiteListExtensions);

        if (!empty($fileExtension) && !in_array($fileExtension, $whiteListExtensions)) {
            //security risk
            throw new \Ip\Exception\Repository\Upload\ForbiddenFileExtension("Files with extension (." . esc(
                $fileExtension
            ) . ") are not permitted for security reasons.", array(
                'extension' => $fileExtension,
                'filename' => $fileName
            ));
        }

        //end security check


        // Look for the content type header
        $contentType = null;
        if (isset($_SERVER["HTTP_CONTENT_TYPE"])) {
            $contentType = $_SERVER["HTTP_CONTENT_TYPE"];
        }

        if (isset($_SERVER["CONTENT_TYPE"])) {
            $contentType = $_SERVER["CONTENT_TYPE"];
        }

        // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
        if (strpos($contentType, "multipart") !== false) {
            if (!isset($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
                throw new \Ip\Exception\Repository\Upload("Failed to move uploaded file.");
            }

            // Open temp file
            $out = fopen($targetDir . $fileName, $chunk == 0 ? "wb" : "ab");
            if (!$out) {
                throw new \Ip\Exception\Repository\Upload("Failed to open output stream.");
            }

            //mark this file as uploaded by current user
            $this->setFileUploadedByThisUser($targetDir . $fileName);

            // Read binary input stream and append it to temp file
            $in = fopen($_FILES['file']['tmp_name'], "rb");

            if (!$in) {
                throw new \Ip\Exception\Repository\Upload("Failed to open input stream.");
            }

            while ($buff = fread($in, 4096)) {
                fwrite($out, $buff);
            }

            fclose($in);
            fclose($out);
            @unlink($_FILES['file']['tmp_name']);
        } else {
            // Open temp file
            $out = fopen($targetDir . '/' . $fileName, $chunk == 0 ? "wb" : "ab");
            if (!$out) {
                throw new \Ip\Exception\Repository\Upload("Failed to open output stream.");
            }

            // Read binary input stream and append it to temp file
            $in = fopen("php://input", "rb");

            if (!$in) {
                throw new \Ip\Exception\Repository\Upload("Failed to open input stream.");
            }

            while ($buff = fread($in, 4096)) {
                if (function_exists('set_time_limit')) {
                    set_time_limit(30);
                }
                fwrite($out, $buff);
            }

            fclose($in);
            fclose($out);
        }

        $this->uploadedFileName = $fileName;
        $this->uploadedFile = $targetDir . $fileName;
        $this->targetDir = $targetDir;
    }

    /**
     * @param string $file relative to BASE_DIR.
     */
    protected function setFileUploadedByThisUser($file)
    {
        $_SESSION['modules']['administrator']['repository']['userFiles'][] = $file;
    }

    /**
     * @param string $file relative to BASE_DIR.
     * @param bool $secure true if we are checking file, uploaded to secure folder. False otherwise
     * @return bool
     */
    public function isFileUploadedByCurrentUser($file, $secure)
    {
        if (!isset($_SESSION['modules']['administrator']['repository']['userFiles'])) {
            return false;
        }
        if ($secure) {
            $targetDir = ipFile('file/secure/tmp/');
        } else {
            $targetDir = ipFile('file/tmp/');
        }

        $isUploaded = in_array($targetDir . $file, $_SESSION['modules']['administrator']['repository']['userFiles']);
        return $isUploaded;
    }

    /**
     * Get file name of uploaded file.
     * @return string
     */
    public function getUploadedFileName()
    {
        return $this->uploadedFileName;
    }

    /**
     * Get path to uploaded file relative to BASE_DIR. File name is also included.
     * @return string
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }


    /**
     * Get dir relative to BASE_DIR where new file has been uploaded.
     * @return string
     */
    public function getTargetDir()
    {
        return $this->targetDir;
    }


    /**
     * return path to uploaded file relative to BASE_DIR
     * @param bool $secure true if we are checking file, uploaded to secure folder. False otherwise*
     * @return string
     */
    public function getUploadedFilePath($fileName, $secure)
    {
        if (!$this->isFileUploadedByCurrentUser($fileName, $secure)) {
            throw new \Ip\Exception\Repository\Upload("This user didn't upload this file or session has ended.");
        }

        return ipFile('file/secure/tmp/' . $fileName);
    }


    protected function folderSize($path)
    {
        $totalSize = 0;
        $files = scandir($path);
        $cleanPath = rtrim($path, '/') . '/';

        foreach ($files as $t) {
            if ($t != "." && $t != "..") {
                $currentFile = $cleanPath . $t;
                if (is_dir($currentFile)) {
                    $size = $this->folderSize($currentFile);
                    $totalSize += $size;
                } else {
                    $size = filesize($currentFile);
                    $totalSize += $size;
                }
            }
        }

        return $totalSize;
    }

}
