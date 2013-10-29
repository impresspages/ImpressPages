<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace Ip\Module\Repository\Transform;

class None extends Base
{
    public function transform($sourceFile, $destinationFile)
    {
        copy($sourceFile, $destinationFile);
    }

}
