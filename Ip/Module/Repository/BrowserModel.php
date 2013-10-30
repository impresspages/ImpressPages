<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Repository;


/**
 * 
 * Functions required by files repository browser
 * 
 * @author Mangirdas
 *
 */
class BrowserModel{
    protected static $instance;

    protected $supportedImageExtensions = array('jpg','jpeg','gif','png');

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
     * @return array
     */
    public function getAvailableFiles($seek, $limit, $filter)
    {
        $answer = array();

        $iterator = new \DirectoryIterator(\Ip\Config::repositoryFile(''));
        $iterator->seek($seek);
        while ($iterator->valid() && count($answer) < $limit) {
            if ($iterator->isFile()) {
                $fileData = $this->getFileData($iterator->getFilename());
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
     * @param $fileName file within file repository directory
     */
    public function getFile($fileName)
    {
        return $this->getFileData($fileName);
    }

    private function getFileData($fileName)
    {
        $file = \Ip\Config::repositoryFile($fileName);
        if (!file_exists($file) || !is_file($file)) {
            throw new Exception("File doesn't exist ".$file);
        }

        $pathInfo = pathinfo($file);
        $ext = strtolower(isset($pathInfo['extension']) ? $pathInfo['extension'] : '');

        $relativeRepositoryDir = \Ip\Config::getRaw('FILE_REPOSITORY_DIR');
        $data = array(
            'fileName' => $fileName,
            'dir' => $relativeRepositoryDir,
            'file' => $relativeRepositoryDir . $fileName,
            'ext' => $ext,
            'preview' => $this->createPreview($relativeRepositoryDir . $fileName),
            'modified' => filemtime($file)
        );
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
            $reflectionService = ReflectionService::instance();
            $transform = new Transform\ImageFit(140, 140, null, TRUE);
            $reflection = $reflectionService->getReflection($file, $baseName, $transform);
            if ($reflection){
                return $reflection;
            }
        }

        return \Ip\config::coreModuleUrl('Repository/public/admin/icons/general.png');
    }

    
    
}