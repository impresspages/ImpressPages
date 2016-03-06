<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace Ip\Internal\Transform;

class None extends \Ip\Transform
{
    public function transform($sourceFile, $destinationFile)
    {
        copy($sourceFile, $destinationFile);
    }

}
