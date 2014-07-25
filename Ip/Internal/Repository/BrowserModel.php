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
     * Get list of files for file browser
     * @param int $seek
     * @param int $limit
     * @param string $filter
     * @param bool $secure use secure folder instead of repository root
     * @return array
     */
    public function getAvailableFiles($seek, $limit, $filter, $secure = false)
    {
        $answer = array();

        $repositoryDir = ipFile('file/repository/');
        if ($secure) {
            $repositoryDir = ipFile('file/secure/');
        }
        $iterator = new \DirectoryIterator($repositoryDir);
        $iterator->seek($seek);
        while ($iterator->valid() && count($answer) < $limit) {
            if ($iterator->isFile()) {
                $fileData = $this->getFileData($iterator->getFilename(), $secure);
                switch ($filter) {
                    case 'image':
                        if (in_array($fileData['ext'], $this->supportedImageExtensions)) {
                            $answer[] = $fileData;
                        }
                        break;
                    default:
                        $answer[] = $fileData;
                        break;
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
    public function getFile($fileName, $secure = false)
    {
        return $this->getFileData($fileName, $secure);
    }

    private function getFileData($fileName, $secure)
    {
        $baseDir = 'file/repository/';
        if ($secure) {
            $baseDir = 'file/secure/';
        }
        $file = ipFile($baseDir . $fileName);
        if (!file_exists($file) || !is_file($file)) {
            throw new \Ip\Exception\Repository("File doesn't exist " . esc($file));
        }

        $pathInfo = pathinfo($file);
        $ext = strtolower(isset($pathInfo['extension']) ? $pathInfo['extension'] : '');

        $data = array(
            'fileName' => $fileName,
            'ext' => $ext,
            'previewUrl' => $this->createPreview($fileName),
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
