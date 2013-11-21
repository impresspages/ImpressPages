<?php
/**
 * @package ImpressPages

 *
 */

namespace IpUpdate\Library\Helper;


class FileSystem
{

    public function createWritableDir($dir)
    {
        //if (substr($dir, 0, 1) != '/') {
            //throw new \IpUpdate\Library\Exception('Absolute path required', \IpUpdate\Library\Exception::OTHER);
        //}
        if ($dir == '/' && !is_writable($dir)) {
            $this->throwWritePermissionsError($dir);
        }

        $dir = $this->removeTrailingSlash($dir); //remove trailing slash
        $parentDir = $this->getParentDir($dir);
         

        if (!file_exists($parentDir) || !is_dir($parentDir)) {
            $this->createWritableDir($parentDir);
        }

        if (!is_writable($parentDir)) {
            $this->throwWritePermissionsError($parentDir);
        }

        if (!file_exists($dir)) {
            mkdir($dir);
        } else {
            $this->makeWritable($dir);
        }

    }


    /**
     * Make directory or file and all subdirs and files writable
     * @param string $dir
     * @param int $permissions eg 0755. ZERO IS REQUIRED. Applied only to files and folders that are not writable.
     * @return boolean
     */
    function makeWritable($path, $permissions = null)
    {


        $answer = true;
        if(!file_exists($path)) {
            return false;
        }

        if (!is_writable($path)) {
            if ($permissions == null) {
                $permissions = $this->getParentPermissions($path);
            }
            $oldErrorHandler = set_error_handler(array('IpUpdate\Library\Helper\FileSystem', 'handleError'));

            try {
                $success = chmod($path, $permissions);
            }
            catch (FileSystemException $e) {
                //do nothing. This is just the way to avoid warnings
            }
            if ($oldErrorHandler) { //dev tools has no oldErrorHandler error handler. So we have to check.
                set_error_handler($oldErrorHandler);
            }
            if (!is_writable($path)) {
                $this->throwWritePermissionsError($path);
            }
        }

        if (is_dir($path)) {
            $path = $this->removeTrailingSlash($path);
            if ($handle = opendir($path)) {
                while (false !== ($file = readdir($handle))) {
                    if($file == ".." || $file == ".") {
                        continue;
                    }
                    if (is_dir($path.'/'.$file)) {
                        $this->makeWritable($path.'/'.$file, $permissions);
                    } else {
                        if (!is_writable($path.'/'.$file)) {
                            if ($permissions == null) {
                                $permissions = $this->getParentPermissions($path);
                            }


                            $oldErrorHandler = set_error_handler(array('IpUpdate\Library\Helper\FileSystem', 'handleError'));
                            try {
                                chmod($path.'/'.$file, $permissions);
                            } catch (FileSystemException $e) {
                                //do nothing. This is just the way to avoid warnings
                            }
                            if ($oldErrorHandler) { //dev tools has no oldErrorHandler error handler. So we have to check.
                                set_error_handler($oldErrorHandler);
                            }
                        }
                        if (!is_writable($path.'/'.$file)) {
                            $this->throwWritePermissionsError($path.'/'.$file);
                        }
                    }
                }
                closedir($handle);
            }
        }


        return $answer;
    }

    public function rm($dir) {

        if (!file_exists($dir)) {
            return;
        }

        if (!is_writable($dir)) {
            $this->makeWritable($dir, 0777);
        }

        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if($file == ".." || $file == ".") {
                        continue;
                    }

                    $this->rm($dir.'/'.$file);
                }
                closedir($handle);
            }

            if (!is_writable($this->getParentDir($dir))) {
                $this->makeWritable($this->getParentDir($dir), 0777);
            }
            rmdir($dir);
        } else {
            unlink($dir);
        }
    }

    /**
     * Remove everything from dir. Make it empty
     * @var string $dir
     */
    public function clean($dir) {
        if (!file_exists($dir) || !is_dir($dir)) {
            throw new \IpUpdate\Library\UpdateException("Directory doesn't exist: ".$dir, \IpUpdate\Library\UpdateException::UNKNOWN);
        }

        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if($file == ".." || $file == ".") {
                    continue;
                }
                $this->rm($dir.'/'.$file);
            }
            closedir($handle);
        }
    }
    /**
     * This is special copy. It copies all content from source into destination directory. But not the source folder it self.
     * @param string $source
     * @param string $dest
     * @throws \Exception
     */
    public function cpContent($source, $dest)
    {
        if (!is_dir($source) || !is_dir($dest)) {
            throw new \IpUpdate\Library\UpdateException("Source or destination is not a folder. Source: ".$source.". Destination: ".$dest."", \IpUpdate\Library\UpdateException::UNKNOWN);
        }

        $dir_handle=opendir($source);
        while($file=readdir($dir_handle)){
            if($file!="." && $file!=".."){
                if(is_dir($source."/".$file)){
                    mkdir($dest."/".$file);
                    $this->cpContent($source."/".$file, $dest."/".$file);
                } else {
                    copy($source."/".$file, $dest."/".$file);
                }
            }
        }
        closedir($dir_handle);
    }

    public function getParentDir($path)
    {
        $path = $this->removeTrailingSlash($path);
        $parentDir = substr($path, 0, strrpos($path, '/') + 1);
        return $parentDir;
    }

    private function throwWritePermissionsError($dir)
    {
        $errorData = array (
            'file' => $dir
        );
        throw new \IpUpdate\Library\UpdateException("Can't write directory", \IpUpdate\Library\UpdateException::WRITE_PERMISSION, $errorData);
    }

    private function getParentPermissions($path)
    {
        return fileperms($this->getParentDir($path));
    }


    private function removeTrailingSlash($path)
    {
        return preg_replace('{/$}', '', $path);
    }



    public static function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        // error was suppressed with the @-operator
        if (0 === error_reporting()) {
            return false;
        }

        throw new FileSystemException($errstr, $errno);
    }


}