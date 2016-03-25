<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Repository;


/**
 *
 * Image related plugins usually need several copies of the same file:
 * original
 * thumbnail
 * small cropped
 * large but smaller than orignial
 * ...
 *
 * It could become a pain to manage all those copies. Old copies should be removed
 * when user crops original photo differently. Or default image sizes changes after theme change.
 *
 * Reflection service takes care of this process. Every time you need a cropped version of
 * image, just use method getReflection and pass cropping options. You will get a path to
 * cropped image. If such version of original doesn't exist, it will be created.
 * You don't need to care about deletion. All copies will be automatically deleted as file
 * will be deleted from the repository.
 *
 * WARNING
 * you can use this class only for images stored in repository (uploaded using default ImpressPages
 * functionality). Otherwise automatic removal is not going to work.
 *
 * Usage example:
 *
 * $reflectionService = \Ip\Internal\Repository\ReflectionService::instance();
 * $transform = new \Ip\Internal\Repository\Transform\ImageFit(100, 100, null, TRUE);
 * $reflection = $reflectionService->getReflection($file, $desiredName, $transform);
 * if (!$reflection){
 *     echo $reflectionService->getLastException()->getMessage();
 *     //do something
 * }
 *
 */
class ReflectionService
{
    protected static $instance;
    protected $lastException = null;

    protected function __construct()
    {

    }

    protected function __clone()
    {

    }

    /**
     * Get singleton instance
     * @return ReflectionService
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new ReflectionService();
        }

        return self::$instance;
    }


    /**
     * @param string $file relative path from file/repository
     * @param array $options - image cropping options
     * @param string $desiredName - desired file name. If reflection is missing, service will try to create new one with name as possible similar to desired
     * @param bool $onDemand transformation will be create on the fly when image accessed for the first time
     * @return string - file name from BASE_DIR
     * @throws \Ip\Exception\Repository\Transform
     */
    public function getReflection($file, $options, $desiredName = null, $onDemand = true)
    {

        $reflectionModel = ReflectionModel::instance();
        try {
            $reflection = $reflectionModel->getReflection($file, $options, $desiredName, $onDemand);
            if (ipConfig()->get('rewritesDisabled') && !is_file(ipFile('file/' . $reflection)) || !ipConfig()->get('realTimeReflections', true)) { //create reflections immediately if mod_rewrite is disabled
                $reflectionRecord = $reflectionModel->getReflectionByReflection($reflection);
                $reflectionModel->createReflection(
                    $reflectionRecord['original'],
                    $reflectionRecord['reflection'],
                    json_decode($reflectionRecord['options'], true)
                );
            }
        } catch (\Exception $e) {
            ipLog()->error($e->getMessage(), array('errorTrace' => $e->getTraceAsString()));
            $this->lastException = $e;
            return false;
        }

        return 'file/' . $reflection;
    }

    /**
     * @return \Ip\Exception\Repository\Transform
     */
    public function getLastException()
    {
        return $this->lastException;
    }


}
