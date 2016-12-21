<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Repository;


/**
 *
 * Functions required by files repository browser
 *
 */
class BrowserModel
{
    protected static $instance;

    protected $supportedImageExtensions = array('jpg', 'jpeg', 'gif', 'png');

    protected function __construct()
    {

    }

    protected function __clone()
    {

    }

    /**
     * Get singleton instance
     * @return BrowserModel
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new BrowserModel();
        }

        return self::$instance;
    }

    /**
     * Throw an exception if path goes out of repository dir
     * @param $path
     * @param $secure
     * @throws \Ip\Exception
     */
    public function pathMustBeInRepository($path, $secure)
    {
        if (!$path) {
            return;
        }
        if ($path && substr($path, -1) != '/') {
            $path .= '/';
        }

        $relativePath = ipFile('file/repository/' . $path);
        if ($secure) {
            $relativePath = ipFile('file/secure/' . $path);
        }

        //check if we are still in the repository dir (to prevent listing files outside of the repository)
        $relpath = realpath($relativePath);
        if ($secure) {
            if (strpos($relpath, realpath(ipFile('file/secure/'))) !== 0) {
                throw new \Ip\Exception("Restricted directory");
            }
        } else {
            if (strpos($relpath, realpath(ipFile('file/repository/'))) !== 0) {
                throw new \Ip\Exception("Restricted directory");
            }
        }

    }

    public function getPath($secure, $subdir, $absolute = true)
    {
        if ($subdir && substr($subdir, -1) != '/') {
            $subdir .= '/';
        }

        $relativePath = 'file/repository/' . $subdir;
        if ($secure) {
            $relativePath = 'file/secure/' . $subdir;
        }



        $path = $relativePath;
        if ($absolute) {
            $path = ipFile($path);
        }
        return $path;
    }

    /**
     * Get list of files for file browser
     * @param int $seek
     * @param int $limit
     * @param string $filter
     * @param bool $secure use secure folder instead of repository root
     * @return array
     */
    public function getAvailableFiles($seek, $limit, $filter, $filterExtensions, $secure = false, $subdir = null)
    {
        $answer = [];
        if ($subdir && substr($subdir, -1) != '/') {
            $subdir .= '/';
        }

        $repositoryDir = $this->getPath($secure, $subdir);


        $iterator = new \DirectoryIterator($repositoryDir);
        $iterator->seek($seek);
        while ($iterator->valid() && count($answer) < $limit) {
            if ($iterator->isFile()) {
                $fileData = $this->getFileData($iterator->getFilename(), $secure, $subdir);
                $append = null;
                switch ($filter) {
                    case 'image':
                        if (in_array($fileData['ext'], $this->supportedImageExtensions)) {
                            $append = $fileData;
                        }
                        break;
                    default:
                        $append = $fileData;
                        break;
                }

                if ($filterExtensions && !in_array($fileData['ext'], $filterExtensions)) {
                    $append = null;
                }

                if ($append) {
                    $answer[] = $append;
                }
            }
            $iterator->next();
        }
        return $answer;
    }

    /**
     * @param string $fileName file within file repository directory
     * @param string $secure
     * @return array
     * @throws \Ip\Exception\Repository
     */
    public function getFile($fileName, $secure = false, $path = null)
    {
        return $this->getFileData($fileName, $secure, $path);
    }

    private function getFileData($fileName, $secure, $subdir = null)
    {
        if ($subdir && substr($subdir, -1) != '/') {
            $subdir .= '/';
        }

        $baseDir = $this->getPath($secure, $subdir, false);

        $file = ipFile($baseDir . $fileName);
        if (!file_exists($file) || !is_file($file)) {
            throw new \Ip\Exception\Repository("File doesn't exist " . esc($file));
        }

        $pathInfo = pathinfo($file);
        $ext = strtolower(isset($pathInfo['extension']) ? $pathInfo['extension'] : '');

        $data = array(
            'fileName' => $subdir . $fileName,
            'ext' => $ext,
            'previewUrl' => $this->createPreview($subdir . $fileName),
            'originalUrl' => ipFileUrl($baseDir . $fileName),
            'modified' => filemtime($file)
        );
        if ($secure) {
            $data['originalUrl'] = null; //secure dir can't be accessed via URL.
        }

        return $data;

    }

    /**
     * Get preview file for file browser
     * @param string $file
     * @return string
     */
    private function createPreview($file)
    {
        $pathInfo = pathinfo($file);
        $ext = strtolower(isset($pathInfo['extension']) ? $pathInfo['extension'] : '');
        $baseName = $pathInfo['basename'];
        if (in_array($ext, $this->supportedImageExtensions)) {
            $transform = array(
                'type' => 'fit',
                'width' => 140,
                'height' => 140,
                'forced' => true
            );
            $reflection = ipReflection($file, $transform, $baseName);
            if ($reflection) {
                return ipFileUrl($reflection);
            }
        }
        return ipFileUrl('Ip/Internal/Repository/assets/icons/general.png');
    }


}
