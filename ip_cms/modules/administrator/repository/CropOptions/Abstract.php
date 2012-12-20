<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 JSC Apro Media.
 * @license   GNU/GPL, see ip_license.html
 */

public abstract class CropOptions
{

    /**
     * @return int
     */
    public abstract function getSourceX1();

    /**
     * @return int
     */
    public abstract function getSourceX2();

    /**
     * @return int
     */
    public abstract function getSourceY1();

    /**
     * @return int
     */
    public abstract function getSourceY2();


    /**
     * @return int
     */
    public abstract function getDestinationWidth();

    /**
     * @return int
     */
    public abstract function getDestinationHeight();

    public function getOptionsKey()
    {
        $allOptions = array(
            $this->getSourceX1(),
            $this->getSourceX2(),
            $this->getSourceY1(),
            $this->getSourceY2(),
            $this->getDestinationWidth(),
            $this->getDestinationHeight()
        );
        $optionsStr = implode(' ', $allOptions);
        return md5($optionsStr);
    }

}
