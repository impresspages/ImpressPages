<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 JSC Apro Media.
 * @license   GNU/GPL, see ip_license.html
 */

namespace IpUpdate\Library;


class Options
{
    private $ignoreFiles = false;

    /**
     * Set or get option to ignore files (don't replace on update)
     * @param null $value
     * @return bool|null
     */
    public function ignoreFiles($value = null)
    {
        if ($value !== null) {
            $this->ignoreFiles = $value;
        }
        return $this->ignoreFiles;
    }

}