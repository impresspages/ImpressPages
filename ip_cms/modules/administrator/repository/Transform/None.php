<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace Modules\administrator\repository\Transform;

class None extends Base
{
    public function transform($sourceFile, $destinationFile)
    {
        copy($sourceFile, $destinationFile);
    }

}
