<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace Ip\Internal\Repository\Transform;

class None extends Base
{
    public function transform($sourceFile, $destinationFile)
    {
        copy($sourceFile, $destinationFile);
    }

}
