<?php
    /**
     * @package ImpressPages

     *
     */
namespace Ip\Module\Repository;


/**
 *
 * Centralized repository to store files. Often the same image needs to be used by many
 * modules / widgets. This class handles these dependences. Use this module to add new files to global
 * files repository. Bind new modules to already existing files. When the file is not bind to any module,
 * it is automatically removed. So bind to existing files, undbind from them and don't whorry if some other
 * modules uses the same files. This class will take care.
 *
 * @author Mangirdas
 *
 */
class UploadModel{

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
     * @throws UploadException
     */
    public function handlePlupload($secureFolder)
    {
        $dispatcher = \Ip\ServiceLocator::getDispatcher();

        if (!$secureFolder && !isset($_SESSION['backend_session']['user_id'])) {
            throw new UploadException("Try to upload image to temporary directory without permission.", UploadException::NO_PERMISSION);
        }

        if ($secureFolder) {
            $targetDir = \Ip\Config::getRaw('TMP_SECURE_DIR');
        } else {
            $targetDir = \Ip\Config::getRaw('TMP_FILE_DIR');
        }

        // Get parameters
        $chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
        $chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

        // Clean the fileName for security reasons
        $fileName = \Library\Php\File\Functions::cleanupFileName($fileName);

        // Make sure the fileName is unique but only if chunking is disabled
        if ($chunks < 2 && file_exists($targetDir.$fileName)) {
            $fileName = \Library\Php\File\Functions::genUnoccupiedName($fileName, $targetDir);
        }





        //security check
        $fileExtension = strtolower(substr($fileName, strrpos($fileName, '.') + 1));

        $event = new Event\ForbiddenExtensions($this);
        $dispatcher->notify($event);
        $disallow = $event->getForbiddenExtensions();
        if (in_array($fileExtension, $disallow)) {
            //security risk
            throw new UploadException("Files with extension (.".$fileExtension.") are not permitted for security reasons.", UploadException::FORBIDDEN_FILE_EXTENSION);
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
                $out = fopen($targetDir.$fileName, $chunk == 0 ? "wb" : "ab");

                if ($out) {
                    //mark this file as uploaded by current user
                    $this->setFileUploadedByThisUser($targetDir.$fileName);

                    // Read binary input stream and append it to temp file
                    $in = fopen($_FILES['file']['tmp_name'], "rb");

                    if ($in) {
                        while ($buff = fread($in, 4096))
                            fwrite($out, $buff);
                    } else {
                        throw new UploadException("Failed to open input stream.", UploadException::INPUT_STREAM_ERROR);
                    }
                    fclose($in);
                    fclose($out);
                    @unlink($_FILES['file']['tmp_name']);
                } else {
                    throw new UploadException("Failed to open output stream.", UploadException::OUTPUT_STREAM_ERROR);
                }
            } else {
                throw new UploadException("Failed to move uploaded file.", UploadException::FAILED_TO_MOVE_UPLOADED_FILE);
            }
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
                } else {
                    throw new UploadException("Failed to open input stream.", UploadException::INPUT_STREAM_ERROR);
                }
                fclose($in);
                fclose($out);
            } else {
                throw new UploadException("Failed to open output stream.", UploadException::OUTPUT_STREAM_ERROR);
            }
        }

        $this->uploadedFileName = $fileName;
        $this->uploadedFile = $targetDir.$fileName;
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
    public function isFileUploadedByCurrentUser($file, $secure) {
        if (!isset($_SESSION['modules']['administrator']['repository']['userFiles'])) {
            return false;
        }
        if ($secure) {
            $targetDir = \Ip\Config::getRaw('TMP_SECURE_DIR');
        } else {
            $targetDir = \Ip\Config::getRaw('TMP_FILE_DIR');
        }

        $isUploaded = in_array($targetDir.$file, $_SESSION['modules']['administrator']['repository']['userFiles']);
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
        if ($this->isFileUploadedByCurrentUser($fileName, $secure)) {
            return TMP_SECURE_DIR.$fileName;
        } else {
            throw new UploadException("This user didn't upload this file or session has ended.", UploadException::SESSION_NOT_FOUND);
        }
    }

}