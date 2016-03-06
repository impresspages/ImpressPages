<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace Ip;

abstract class Transform
{
    /**
     * @param string $sourceFile original file
     * @param string $destinationFile destination file with extension provided by getNewExtension
     * @return mixed
     */
    public abstract function transform($sourceFile, $destinationFile);

    /**
     * Transform function might want to change file extension.
     * @param string $sourceFile original file
     * @param string $ext original file extension
     * @return string
     */
    public function getNewExtension($sourceFile, $ext)
    {
        return $ext; //by default extension doesn't change. Overwrite this method to return different extension
    }

    public function getParamStr()
    {
        return serialize(get_object_vars($this));

    }

    final public function getFingerprint()
    {
        return md5(__CLASS__ . ':' . $this->getParamStr());
    }

}
