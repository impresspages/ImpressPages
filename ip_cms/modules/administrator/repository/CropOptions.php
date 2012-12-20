<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 JSC Apro Media.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Modules\administrator\repository;

class CropOptions
{
    protected $sourceX1;
    protected $sourceX2;
    protected $sourceY1;
    protected $sourceY2;
    protected $destinationWidth;
    protected $destinationHeight;

    public function __construct($sourceX1, $sourceX2, $sourceY1, $sourceY2, $destinationWidth, $destinationHeight)
    {
        if ($sourceX2 - $sourceX1 < 1) {
            throw new \Exception("Destination width can't be less than 1");
        }

        if ($sourceY2 - $sourceY1 < 1) {
            throw new \Exception("Destination height can't be less than 1");
        }

        if ($destinationWidth < 1) {
            throw new \Exception("Destination width can't be less than 1");
        }
        if ($destinationHeight < 1) {
            throw new \Exception("Destination width can't be less than 1");
        }
    }

    /**
     * @return int
     */
    public function getSourceX1()
    {
        return $this->sourceX1;
    }

    /**
     * @return int
     */
    public function getSourceX2()
    {
        return $this->sourceX2;
    }

    /**
     * @return int
     */
    public function getSourceY1()
    {
        return $this->sourceY1;
    }


    /**
     * @return int
     */
    public function getSourceY2()
    {
        return $this->sourceY2;
    }



    /**
     * @return int
     */
    public function getDestinationWidth()
    {
        return $this->destinationWidth;
    }


    /**
     * @return int
     */
    public function getDestinationHeight()
    {
        return $this->destinationHeight;
    }

    /**
     * @return int
     */
    public function getSourceWidth()
    {
        return $this->getSourceX2() - $this->getSourceX1();
    }

    /**
     * @return int
     */
    public function getSourceHeight()
    {
        return $this->getSourceY2() - $this->getSourceY1();
    }



}