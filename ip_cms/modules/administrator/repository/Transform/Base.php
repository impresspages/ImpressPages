<?php
    /**
     * @package   ImpressPages
     * @copyright Copyright (C) 2012 JSC Apro Media.
     * @license   GNU/GPL, see ip_license.html
     */

namespace Modules\administrator\repository\Transform;

abstract class Base
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
        return md5(__CLASS__.':'.$this->getParamStr());
    }

}
