<?php
/**
 * @package   ImpressPages
 *
 *
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