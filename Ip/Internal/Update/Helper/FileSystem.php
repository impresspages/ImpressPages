<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Update\Helper;

class FileSystem
{

    public function createWritableDir($dir)
    {
        if (substr($dir, 0, 1) != '/' && $dir[1] != ':') { // Check if absolute path: '/' for unix, 'C:' for Windows
            throw new \Ip\Internal\Update\UpdateException('Absolute path required');
        }
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
     * @param string $path
     * @param int $permissions eg 0755. ZERO IS REQUIRED. Applied only to files and folders that are not writable.
     * @return boolean
     */
    function makeWritable($path, $permissions = null)
    {
        if ($permissions == null) {
            $permissions = $this->getParentPermissions($path);
        }

        $answer = true;
        if (!file_exists($path)) {
            return false;
        }

        if (!is_writable($path)) {

            $oldErrorHandler = set_error_handler(array('Ip\Internal\Update\Helper\FileSystem', 'handleError'));

            try {
                $originalIpErrorHandler = set_error_handler('Ip\Internal\ErrorHandler::ipSilentErrorHandler');
                chmod($path, $permissions);
                set_error_handler($originalIpErrorHandler);
            } catch (FileSystemException $e) {
                //do nothing. This is just the way to avoid warnings
            }
            set_error_handler($oldErrorHandler);

            if (!is_writable($path)) {
                $this->throwWritePermissionsError($path);
            }
        }

        if (is_dir($path)) {
            $path = $this->removeTrailingSlash($path);
            if ($handle = opendir($path)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file == ".." || $file == ".") {
                        continue;
                    }
                    if (is_dir($path . '/' . $file)) {
                        $this->makeWritable($path . '/' . $file, $permissions);
                    } else {
                        if (!is_writable($path . '/' . $file)) {
                            chmod($path . '/' . $file, $permissions);
                        }
                        if (!is_writable($path . '/' . $file)) {
                            $this->throwWritePermissionsError($path . '/' . $file);
                        }
                    }
                }
                closedir($handle);
            }
        }


        return $answer;
    }

    public function rm($dir)
    {

        if (!file_exists($dir)) {
            return;
        }

        $originalIpErrorHandler = set_error_handler('Ip\Internal\ErrorHandler::ipSilentErrorHandler');
        chmod($dir, 0777);
        set_error_handler($originalIpErrorHandler);
        if (!is_writable($dir)) {
            throw new \Ip\Internal\Update\UpdateException("Directory is not writable: " . $dir);
        }

        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file == ".." || $file == ".") {
                        continue;
                    }

                    $this->rm($dir . '/' . $file);
                }
                closedir($handle);
            }

            rmdir($dir);
        } else {
            unlink($dir);
        }
    }

    /**
     * Remove everything from dir. Make it empty
     * @var string $dir
     * @throws \Ip\Internal\Update\UpdateException
     */
    public function clean($dir)
    {
        if (!file_exists($dir) || !is_dir($dir)) {
            throw new \Ip\Internal\Update\UpdateException("Directory doesn't exist: " . $dir);
        }

        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file == ".." || $file == ".") {
                    continue;
                }
                $this->rm($dir . '/' . $file);
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
            throw new \Ip\Internal\Update\UpdateException("Source or destination is not a folder. Source: " . $source . ". Destination: " . $dest . "");
        }

        $dir_handle = opendir($source);
        while ($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (is_dir($source . "/" . $file)) {
                    mkdir($dest . "/" . $file);
                    $this->cpContent($source . "/" . $file, $dest . "/" . $file);
                } else {
                    copy($source . "/" . $file, $dest . "/" . $file);
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
        throw new \Ip\Internal\Update\UpdateException("Can't write directory " . $dir);
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
        throw new \Ip\Internal\Update\UpdateException($errstr);
    }


}
